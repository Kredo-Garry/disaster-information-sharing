// src/components/HomeEarthquakeBlock.jsx
import React, { useEffect, useMemo, useRef, useState } from "react";

/**
 * HomeEarthquakeBlock
 * - Laravel側の API: GET /api/home-earthquakes
 * - 取得した地震データを /home で表示する用コンポーネント
 *
 * 変更点（2026-02-xx）:
 * - 表示件数は pageSize (=5) に固定し、ページ切替で全件確認できるようにした
 * - 取得件数は fetchLimit (=50) を使う（表示とは別）
 */
export default function HomeEarthquakeBlock({
  apiBaseUrl = "",
  pageSize = 5, // ★ 表示：1ページの件数
  fetchLimit = 50, // ★ 取得：APIから取ってくる件数（ページングの母数）
  title = "Latest PHIVOLCS Earthquakes",
}) {
  const [items, setItems] = useState([]);
  const [status, setStatus] = useState("idle"); // idle | loading | success | error
  const [error, setError] = useState("");
  const [page, setPage] = useState(1);
  const abortRef = useRef(null);

  const endpoint = useMemo(() => {
    const base = (apiBaseUrl || "").replace(/\/+$/, "");
    const url = `${base}/api/home-earthquakes`;
    const sep = url.includes("?") ? "&" : "?";
    return `${url}${sep}limit=${encodeURIComponent(fetchLimit)}`;
  }, [apiBaseUrl, fetchLimit]);

  const totalPages = useMemo(() => {
    const n = Math.max(1, Math.ceil((items?.length || 0) / Math.max(1, pageSize)));
    return n;
  }, [items, pageSize]);

  const pageItems = useMemo(() => {
    const size = Math.max(1, pageSize);
    const p = Math.min(Math.max(1, page), totalPages);
    const start = (p - 1) * size;
    return (items || []).slice(start, start + size);
  }, [items, page, pageSize, totalPages]);

  const updatedLabel = useMemo(() => {
    if (!items.length) return "";
    const fetchedAt = items[0]?.fetched_at;
    if (!fetchedAt) return "";
    return `Updated: ${formatDateTime(fetchedAt)}`;
  }, [items]);

  const doFetch = async () => {
    setStatus("loading");
    setError("");

    if (abortRef.current) abortRef.current.abort();
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

      const normalized = arr
        .map((x) => normalizeEq(x))
        .filter((x) => x && x.lat != null && x.lng != null);

      setItems(normalized);
      setPage(1); // ★ 新しいデータになったら1ページ目に戻す
      setStatus("success");
    } catch (e) {
      if (e?.name === "AbortError") return;
      setStatus("error");
      setError(e?.message || "Failed to load earthquakes.");
    }
  };

  useEffect(() => {
    doFetch();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [endpoint]);

  return (
    <section style={styles.wrap}>
      <div style={styles.headerRow}>
        <div>
          <h3 style={styles.title}>{title}</h3>
          {updatedLabel ? <div style={styles.subtle}>{updatedLabel}</div> : null}
          <div style={styles.subtle}>
            Showing {Math.min(pageSize, pageItems.length)} / {items.length || 0} (page {Math.min(page, totalPages)} of{" "}
            {totalPages})
          </div>
        </div>

        <button
          type="button"
          style={styles.refreshBtn}
          onClick={doFetch}
          disabled={status === "loading"}
          aria-busy={status === "loading" ? "true" : "false"}
        >
          {status === "loading" ? "Loading..." : "Refresh"}
        </button>
      </div>

      {totalPages > 1 ? (
        <div style={styles.pagerRow}>
          <button
            type="button"
            style={{ ...styles.pagerBtn, opacity: page <= 1 ? 0.45 : 1 }}
            onClick={() => setPage((p) => Math.max(1, p - 1))}
            disabled={page <= 1 || status === "loading"}
          >
            ← Prev
          </button>

          <div style={styles.pagerInfo}>
            Page <b>{Math.min(page, totalPages)}</b> / <b>{totalPages}</b>
          </div>

          <button
            type="button"
            style={{ ...styles.pagerBtn, opacity: page >= totalPages ? 0.45 : 1 }}
            onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
            disabled={page >= totalPages || status === "loading"}
          >
            Next →
          </button>
        </div>
      ) : null}

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

      {status === "loading" ? <SkeletonList rows={Math.min(5, pageSize)} /> : null}

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
          {pageItems.map((eq) => (
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

function SkeletonList({ rows = 5 }) {
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
  const s = String(input);
  const iso = s.includes("T") ? s : s.replace(" ", "T") + (s.endsWith("Z") ? "" : "");
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return s;
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
    // 地震：薄い茶色（既に入れている場合はそのまま）
    background: "rgba(150, 75, 0, 0.06)",
    borderRadius: 18,
    marginTop: 8,
    marginBottom: 8,
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
  pagerRow: {
    display: "flex",
    alignItems: "center",
    justifyContent: "space-between",
    gap: 10,
    marginBottom: 10,
  },
  pagerBtn: {
    border: "1px solid rgba(0,0,0,0.12)",
    background: "#fff",
    padding: "7px 10px",
    borderRadius: 999,
    cursor: "pointer",
    fontSize: 12,
    whiteSpace: "nowrap",
  },
  pagerInfo: {
    fontSize: 12,
    opacity: 0.85,
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
