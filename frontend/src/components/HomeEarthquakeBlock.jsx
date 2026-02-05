// src/components/HomeEarthquakeBlock.jsx
import React, { useEffect, useMemo, useRef, useState } from "react";

/**
 * HomeEarthquakeBlock
 * - Laravel側の API: GET /api/home-earthquakes
 * - 取得した地震データを /home で表示する用コンポーネント
 *
 * 使い方:
 *   import HomeEarthquakeBlock from "./components/HomeEarthquakeBlock";
 *   <HomeEarthquakeBlock />
 *
 * オプション:
 *   <HomeEarthquakeBlock apiBaseUrl="http://localhost:8000" />
 *   <HomeEarthquakeBlock limit={10} />
 */
export default function HomeEarthquakeBlock({
  apiBaseUrl = "", // 例: "http://localhost:8000"。空なら同一オリジン想定
  limit = 10,
  title = "Latest PHIVOLCS Earthquakes",
}) {
  const [items, setItems] = useState([]);
  const [status, setStatus] = useState("idle"); // idle | loading | success | error
  const [error, setError] = useState("");
  const abortRef = useRef(null);

  const endpoint = useMemo(() => {
    const base = (apiBaseUrl || "").replace(/\/+$/, "");
    // 現状のLaravel側は limit 固定でもOK。将来対応のため limit を query に付ける。
    const url = `${base}/api/home-earthquakes`;
    const sep = url.includes("?") ? "&" : "?";
    return `${url}${sep}limit=${encodeURIComponent(limit)}`;
  }, [apiBaseUrl, limit]);

  useEffect(() => {
    setStatus("loading");
    setError("");

    if (abortRef.current) abortRef.current.abort();
    const controller = new AbortController();
    abortRef.current = controller;

    (async () => {
      try {
        const res = await fetch(endpoint, {
          method: "GET",
          headers: { Accept: "application/json" },
          signal: controller.signal,
        });

        if (!res.ok) {
          const txt = await safeReadText(res);
          throw new Error(`HTTP ${res.status} ${res.statusText}${txt ? ` - ${txt}` : ""}`);
        }

        const data = await res.json();
        const arr = Array.isArray(data?.earthquakes) ? data.earthquakes : [];

        // 念のため最低限の整形
        const normalized = arr
          .map((x) => normalizeEq(x))
          .filter((x) => x && x.lat != null && x.lng != null);

        setItems(normalized);
        setStatus("success");
      } catch (e) {
        if (e?.name === "AbortError") return;
        setStatus("error");
        setError(e?.message || "Failed to load earthquakes.");
      }
    })();

    return () => controller.abort();
  }, [endpoint]);

  const updatedLabel = useMemo(() => {
    if (!items.length) return "";
    // APIは fetched_at を返してない仕様でもOK。返ってる場合だけ表示。
    const fetchedAt = items[0]?.fetched_at;
    if (!fetchedAt) return "";
    return `Updated: ${formatDateTime(fetchedAt)}`;
  }, [items]);

  return (
    <section style={styles.wrap}>
      <div style={styles.headerRow}>
        <div>
          <h3 style={styles.title}>{title}</h3>
          {updatedLabel ? <div style={styles.subtle}>{updatedLabel}</div> : null}
        </div>

        <button
          type="button"
          style={styles.refreshBtn}
          onClick={() => {
            // endpointは同じでも、再実行したいので useEffect をトリガーする簡易策
            // ＝ abort -> fetch をもう一回
            setStatus("loading");
            setError("");
            if (abortRef.current) abortRef.current.abort();
            // endpoint 依存の effect を待たずに即 fetch したいので、window.locationはしない
            // ここは effect の endpoint をそのまま使って再フェッチする
            (async () => {
              const controller = new AbortController();
              abortRef.current = controller;
              try {
                const res = await fetch(endpoint, {
                  method: "GET",
                  headers: { Accept: "application/json" },
                  signal: controller.signal,
                });
                if (!res.ok) {
                  const txt = await safeReadText(res);
                  throw new Error(`HTTP ${res.status} ${res.statusText}${txt ? ` - ${txt}` : ""}`);
                }
                const data = await res.json();
                const arr = Array.isArray(data?.earthquakes) ? data.earthquakes : [];
                const normalized = arr.map((x) => normalizeEq(x)).filter(Boolean);
                setItems(normalized);
                setStatus("success");
              } catch (e) {
                if (e?.name === "AbortError") return;
                setStatus("error");
                setError(e?.message || "Failed to load earthquakes.");
              }
            })();
          }}
          disabled={status === "loading"}
          aria-busy={status === "loading" ? "true" : "false"}
        >
          {status === "loading" ? "Loading..." : "Refresh"}
        </button>
      </div>

      {status === "error" ? (
        <div style={styles.errorBox}>
          <div style={styles.errorTitle}>Failed to load</div>
          <div style={styles.errorMsg}>{error}</div>
          <div style={styles.errorHint}>
            Check that Laravel is running at <code>http://localhost:8000</code> and the endpoint{" "}
            <code>/api/home-earthquakes</code> returns JSON.
          </div>
        </div>
      ) : null}

      {status === "loading" ? <SkeletonList rows={Math.min(6, limit)} /> : null}

      {status === "success" && items.length === 0 ? (
        <div style={styles.emptyBox}>
          <div style={styles.emptyTitle}>No earthquake data yet</div>
          <div style={styles.subtle}>
            Run: <code>php artisan phivolcs:fetch-earthquakes --limit=10</code>
          </div>
        </div>
      ) : null}

      {status === "success" && items.length > 0 ? (
        <div style={styles.list}>
          {items.slice(0, limit).map((eq) => (
            <EarthquakeCard key={eq.id ?? eq.hash ?? `${eq.lat}-${eq.lng}-${eq.occurred_at}`} eq={eq} />
          ))}
        </div>
      ) : null}
    </section>
  );
}

/* -------------------------- UI bits -------------------------- */

function EarthquakeCard({ eq }) {
  const occurred = eq.occurred_at ? formatDateTime(eq.occurred_at) : "—";
  const mag = eq.magnitude != null ? Number(eq.magnitude).toFixed(1) : "—";
  const depth = eq.depth_km != null ? `${Number(eq.depth_km)} km` : "—";
  const coords =
    eq.lat != null && eq.lng != null ? `${Number(eq.lat).toFixed(2)}, ${Number(eq.lng).toFixed(2)}` : "—";

  const magBadgeStyle = getMagBadgeStyle(eq.magnitude);

  return (
    <article style={styles.card}>
      <div style={styles.cardTopRow}>
        <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
          <span style={{ ...styles.badge, ...magBadgeStyle }}>M {mag}</span>
          <div style={styles.cardTitle}>{eq.location_text || "Unknown location"}</div>
        </div>

        <div style={styles.cardMetaRight}>
          <div style={styles.metaLine}>
            <span style={styles.metaLabel}>Depth</span>
            <span>{depth}</span>
          </div>
          <div style={styles.metaLine}>
            <span style={styles.metaLabel}>Coords</span>
            <span>{coords}</span>
          </div>
        </div>
      </div>

      <div style={styles.cardBottomRow}>
        <div style={styles.subtle}>
          <span style={styles.metaLabel}>Time</span> {occurred}
        </div>

        {eq.source_url ? (
          <a href={eq.source_url} target="_blank" rel="noreferrer" style={styles.link}>
            Source
          </a>
        ) : null}
      </div>
    </article>
  );
}

function SkeletonList({ rows = 6 }) {
  return (
    <div style={styles.list}>
      {Array.from({ length: rows }).map((_, i) => (
        <div key={i} style={styles.skelCard}>
          <div style={styles.skelRow}>
            <div style={{ ...styles.skelBlock, width: 70, height: 26 }} />
            <div style={{ ...styles.skelBlock, width: "60%", height: 18 }} />
          </div>
          <div style={styles.skelRow}>
            <div style={{ ...styles.skelBlock, width: "35%", height: 14 }} />
            <div style={{ ...styles.skelBlock, width: "25%", height: 14 }} />
          </div>
        </div>
      ))}
    </div>
  );
}

/* -------------------------- helpers -------------------------- */

function normalizeEq(x) {
  if (!x || typeof x !== "object") return null;
  return {
    id: x.id ?? null,
    hash: x.hash ?? null,
    occurred_at: x.occurred_at ?? null,
    magnitude: x.magnitude ?? null,
    depth_km: x.depth_km ?? null,
    lat: x.lat ?? null,
    lng: x.lng ?? null,
    location_text: x.location_text ?? "",
    source_url: x.source_url ?? "",
    fetched_at: x.fetched_at ?? null,
  };
}

async function safeReadText(res) {
  try {
    const t = await res.text();
    return t?.slice(0, 300) || "";
  } catch {
    return "";
  }
}

function formatDateTime(input) {
  // input が "2026-02-04 16:30:00" のような MySQL形式なら Date が解釈しづらいので補正
  const s = String(input);
  const iso = s.includes("T") ? s : s.replace(" ", "T") + (s.endsWith("Z") ? "" : "");
  const d = new Date(iso);

  // Date変換に失敗したら元文字列返す
  if (Number.isNaN(d.getTime())) return s;

  // 表示は読みやすい形式
  return d.toLocaleString(undefined, {
    year: "numeric",
    month: "short",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });
}

function getMagBadgeStyle(magnitude) {
  const m = Number(magnitude);
  if (!Number.isFinite(m)) return { background: "#e9ecef", color: "#111" };
  if (m >= 6) return { background: "#dc3545", color: "#fff" };
  if (m >= 5) return { background: "#fd7e14", color: "#fff" };
  if (m >= 4) return { background: "#ffc107", color: "#111" };
  if (m >= 3) return { background: "#0d6efd", color: "#fff" };
  return { background: "#198754", color: "#fff" };
}

/* -------------------------- inline styles -------------------------- */

const styles = {
  wrap: {
    width: "100%",
    maxWidth: 980,
    margin: "0 auto",
    padding: "16px 14px",
    boxSizing: "border-box",
  },
  headerRow: {
    display: "flex",
    alignItems: "flex-start",
    justifyContent: "space-between",
    gap: 12,
    marginBottom: 12,
  },
  title: {
    margin: 0,
    fontSize: 18,
    fontWeight: 700,
    lineHeight: 1.2,
  },
  subtle: {
    marginTop: 4,
    fontSize: 12,
    opacity: 0.75,
  },
  refreshBtn: {
    border: "1px solid rgba(0,0,0,0.15)",
    background: "#fff",
    padding: "8px 10px",
    borderRadius: 10,
    cursor: "pointer",
    fontSize: 13,
    whiteSpace: "nowrap",
  },
  list: {
    display: "grid",
    gridTemplateColumns: "1fr",
    gap: 10,
  },
  card: {
    border: "1px solid rgba(0,0,0,0.12)",
    borderRadius: 14,
    padding: 12,
    background: "#fff",
    boxShadow: "0 1px 6px rgba(0,0,0,0.04)",
  },
  cardTopRow: {
    display: "flex",
    justifyContent: "space-between",
    gap: 12,
    alignItems: "flex-start",
  },
  cardTitle: {
    fontSize: 14,
    fontWeight: 700,
    lineHeight: 1.3,
  },
  cardMetaRight: {
    display: "grid",
    gap: 6,
    minWidth: 170,
    textAlign: "right",
    fontSize: 12,
    opacity: 0.9,
  },
  metaLine: {
    display: "flex",
    justifyContent: "flex-end",
    gap: 8,
  },
  metaLabel: {
    opacity: 0.7,
    fontWeight: 600,
  },
  cardBottomRow: {
    marginTop: 10,
    display: "flex",
    alignItems: "center",
    justifyContent: "space-between",
    gap: 10,
  },
  link: {
    fontSize: 12,
    textDecoration: "none",
    border: "1px solid rgba(0,0,0,0.12)",
    padding: "6px 10px",
    borderRadius: 999,
    color: "inherit",
  },
  badge: {
    display: "inline-flex",
    alignItems: "center",
    height: 26,
    padding: "0 10px",
    borderRadius: 999,
    fontSize: 12,
    fontWeight: 800,
    letterSpacing: 0.2,
  },
  errorBox: {
    border: "1px solid rgba(220,53,69,0.35)",
    background: "rgba(220,53,69,0.06)",
    padding: 12,
    borderRadius: 14,
    marginBottom: 12,
  },
  errorTitle: { fontWeight: 800, marginBottom: 6 },
  errorMsg: { fontSize: 13, marginBottom: 6 },
  errorHint: { fontSize: 12, opacity: 0.8 },
  emptyBox: {
    border: "1px solid rgba(0,0,0,0.12)",
    background: "rgba(0,0,0,0.02)",
    padding: 14,
    borderRadius: 14,
  },
  emptyTitle: { fontWeight: 800, marginBottom: 6 },
  skelCard: {
    border: "1px solid rgba(0,0,0,0.12)",
    borderRadius: 14,
    padding: 12,
    background: "#fff",
  },
  skelRow: { display: "flex", gap: 10, alignItems: "center", marginBottom: 10 },
  skelBlock: {
    background: "rgba(0,0,0,0.08)",
    borderRadius: 10,
  },
};
