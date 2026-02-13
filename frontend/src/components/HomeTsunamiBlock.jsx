import React, { useEffect, useMemo, useRef, useState } from "react";

export default function HomeTsunamiBlock({
  apiBaseUrl = "",
  pageSize = 5,     // ★ 表示は5件
  fetchLimit = 50,  // ★ 取得は多め（ページング母数）
  title = "Latest PHIVOLCS Tsunami Bulletins",
}) {
  const [items, setItems] = useState([]);
  const [status, setStatus] = useState("idle");
  const [error, setError] = useState("");
  const [page, setPage] = useState(1);
  const abortRef = useRef(null);

  const endpoint = useMemo(() => {
    const base = (apiBaseUrl || "").replace(/\/+$/, "");
    // ✅ 正しいAPIパスに修正（route:list に合わせる）
    const url = `${base}/api/home/tsunami`;
    const sep = url.includes("?") ? "&" : "?";
    return `${url}${sep}limit=${encodeURIComponent(fetchLimit)}`;
  }, [apiBaseUrl, fetchLimit]);

  const totalPages = useMemo(() => {
    return Math.max(1, Math.ceil((items?.length || 0) / Math.max(1, pageSize)));
  }, [items, pageSize]);

  const pageItems = useMemo(() => {
    const size = Math.max(1, pageSize);
    const p = Math.min(Math.max(1, page), totalPages);
    const start = (p - 1) * size;
    return (items || []).slice(start, start + size);
  }, [items, page, pageSize, totalPages]);

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

        // ✅ 返却キーの揺れに強くする（tsunami / tsunamis / items）
        const arr =
          (Array.isArray(data?.tsunami) && data.tsunami) ||
          (Array.isArray(data?.tsunamis) && data.tsunamis) ||
          (Array.isArray(data?.items) && data.items) ||
          [];

        setItems(arr);
        setPage(1);
        setStatus("success");
      } catch (e) {
        if (e?.name === "AbortError") return;
        setStatus("error");
        setError(e?.message || "Failed to load tsunami bulletins.");
      }
    })();

    return () => controller.abort();
  }, [endpoint]);

  return (
    <section style={styles.wrap}>
      <div style={styles.headerRow}>
        <div>
          <h3 style={styles.title}>{title}</h3>
          <div style={styles.subtle}>
            Showing {Math.min(pageSize, pageItems.length)} / {items.length || 0} (page {Math.min(page, totalPages)} of{" "}
            {totalPages})
          </div>
        </div>
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
          <div style={styles.hint}>
            If React is :3000 and Laravel is :8000, set API Base URL to <code>http://localhost:8000</code>. Endpoint:{" "}
            <code>/api/home/tsunami</code>
          </div>
        </div>
      ) : null}

      {status === "loading" ? <div style={styles.subtle}>Loading...</div> : null}

      {status === "success" && items.length === 0 ? (
        <div style={styles.emptyBox}>
          <div style={styles.emptyTitle}>No tsunami bulletin saved yet</div>
          <div style={styles.subtle}>
            Run: <code>php artisan phivolcs:fetch-tsunami --cafile=/usr/local/etc/ssl/cacert_plus_phivolcs.pem</code>
          </div>
        </div>
      ) : null}

      {status === "success" && items.length > 0 ? (
        <div style={styles.list}>
          {pageItems.map((x) => (
            <article key={x.id ?? x.hash} style={styles.card}>
              <div style={styles.cardTop}>
                <div style={styles.badge}>{x.status || "Tsunami"}</div>
                <div style={styles.cardTitle}>{x.bulletin_no || "Latest bulletin"}</div>
              </div>

              <div style={styles.subtle}>
                <b>Issued:</b> {x.issued_at ? formatDateTime(x.issued_at) : "—"}
              </div>

              {x.summary_text ? <div style={styles.body}>{String(x.summary_text).slice(0, 320)}</div> : null}

              {x.source_url ? (
                <a href={x.source_url} target="_blank" rel="noreferrer" style={styles.link}>
                  Source
                </a>
              ) : null}
            </article>
          ))}
        </div>
      ) : null}
    </section>
  );
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
  const iso = s.includes("T") ? s : s.replace(" ", "T");
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return s;

  return d.toLocaleString("en-US", {
    year: "numeric",
    month: "short",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    hour12: false,
  });
}

const styles = {
  wrap: {
    maxWidth: 980,
    margin: "0 auto",
    padding: "16px 14px",
    boxSizing: "border-box",
    background: "rgba(0, 180, 216, 0.06)",
    borderRadius: 18,
    marginTop: 8,
    marginBottom: 8,
  },
  headerRow: { display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 12 },
  title: { margin: 0, fontSize: 18, fontWeight: 700 },
  subtle: { fontSize: 12, opacity: 0.75, marginTop: 6 },
  pagerRow: { display: "flex", alignItems: "center", justifyContent: "space-between", gap: 10, marginBottom: 10 },
  pagerBtn: {
    border: "1px solid rgba(0,0,0,0.12)",
    background: "#fff",
    padding: "7px 10px",
    borderRadius: 999,
    cursor: "pointer",
    fontSize: 12,
    whiteSpace: "nowrap",
  },
  pagerInfo: { fontSize: 12, opacity: 0.85 },
  list: { display: "grid", gridTemplateColumns: "1fr", gap: 10 },
  card: { border: "1px solid rgba(0,0,0,0.12)", borderRadius: 14, padding: 12, background: "#fff" },
  cardTop: { display: "flex", alignItems: "center", gap: 10, marginBottom: 8 },
  badge: { fontSize: 12, fontWeight: 800, padding: "5px 10px", borderRadius: 999, background: "rgba(0,0,0,0.06)" },
  cardTitle: { fontSize: 14, fontWeight: 800 },
  body: { marginTop: 8, fontSize: 13, lineHeight: 1.5, opacity: 0.9 },
  link: {
    display: "inline-block",
    marginTop: 10,
    fontSize: 12,
    textDecoration: "none",
    border: "1px solid rgba(0,0,0,0.12)",
    padding: "6px 10px",
    borderRadius: 999,
    color: "inherit",
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
  hint: { fontSize: 12, opacity: 0.8 },
  emptyBox: { border: "1px solid rgba(0,0,0,0.12)", background: "rgba(0,0,0,0.02)", padding: 14, borderRadius: 14 },
  emptyTitle: { fontWeight: 800, marginBottom: 6 },
};
