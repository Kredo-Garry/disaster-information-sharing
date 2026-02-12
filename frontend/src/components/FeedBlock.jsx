// frontend/src/components/FeedBlock.jsx
import React, { useEffect, useMemo, useState } from "react";

/**
 * DIShiP /feed 用 FeedBlock（Feedモデル対応版）
 *
 * API: GET {apiBaseUrl}/api/feed?page=1&pageSize=10&tag=earthquake&platform=facebook
 * 期待するレスポンス:
 * {
 *   data: [
 *     {
 *       id,
 *       source_platform,
 *       external_author,
 *       content,
 *       original_url,
 *       tags: [],
 *       published_at,
 *       embed_html
 *     }
 *   ],
 *   meta: { page, pageSize, total, totalPages }
 * }
 */
export default function FeedBlock({
  title = "Disaster Feed",
  apiBaseUrl = "http://localhost:8000",
  pageSize = 10,
  defaultTag = "",
  defaultPlatform = "", // 例: "facebook"
}) {
  const [tag, setTag] = useState(defaultTag);
  const [platform, setPlatform] = useState(defaultPlatform);
  const [page, setPage] = useState(1);

  const [items, setItems] = useState([]);
  const [meta, setMeta] = useState({ page: 1, pageSize, total: 0, totalPages: 1 });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const endpoint = useMemo(() => {
    const base = apiBaseUrl.replace(/\/$/, "");
    const u = new URL(`${base}/api/feed`);
    u.searchParams.set("page", String(page));
    u.searchParams.set("pageSize", String(pageSize));
    if (tag) u.searchParams.set("tag", tag);
    if (platform) u.searchParams.set("platform", platform);
    return u.toString();
  }, [apiBaseUrl, page, pageSize, tag, platform]);

  useEffect(() => {
    let cancelled = false;

    (async () => {
      try {
        setLoading(true);
        setError("");

        const res = await fetch(endpoint, { headers: { Accept: "application/json" } });
        if (!res.ok) throw new Error(`API error: ${res.status}`);

        const json = await res.json();
        if (cancelled) return;

        setItems(Array.isArray(json?.data) ? json.data : []);
        setMeta(
          json?.meta ?? {
            page: 1,
            pageSize,
            total: 0,
            totalPages: 1,
          }
        );
      } catch (e) {
        if (!cancelled) setError(e?.message || "Failed to load feed");
      } finally {
        if (!cancelled) setLoading(false);
      }
    })();

    return () => {
      cancelled = true;
    };
  }, [endpoint, pageSize]);

  const canPrev = (meta?.page ?? 1) > 1;
  const canNext = (meta?.page ?? 1) < (meta?.totalPages ?? 1);

  const onChangeTag = (v) => {
    setPage(1);
    setTag(v);
  };

  const onChangePlatform = (v) => {
    setPage(1);
    setPlatform(v);
  };

  return (
    <div style={{ borderRadius: 16, padding: 16, background: "#fff" }}>
      <div style={{ display: "flex", gap: 12, alignItems: "center", justifyContent: "space-between" }}>
        <h2 style={{ margin: 0 }}>{title}</h2>

        <div style={{ display: "flex", gap: 8, alignItems: "center", flexWrap: "wrap", justifyContent: "flex-end" }}>
          <select
            value={tag}
            onChange={(e) => onChangeTag(e.target.value)}
            style={{ padding: "8px 10px", borderRadius: 10 }}
          >
            <option value="">All tags</option>
            <option value="earthquake">Earthquake</option>
            <option value="tsunami">Tsunami</option>
            <option value="volcano">Volcano</option>
            <option value="flood">Flood</option>
            <option value="landslide">Landslide</option>
          </select>

          <select
            value={platform}
            onChange={(e) => onChangePlatform(e.target.value)}
            style={{ padding: "8px 10px", borderRadius: 10 }}
          >
            <option value="">All platforms</option>
            <option value="facebook">Facebook</option>
            <option value="web">Web</option>
            <option value="x">X</option>
          </select>
        </div>
      </div>

      {loading && <p style={{ marginTop: 12 }}>Loading...</p>}
      {error && <p style={{ marginTop: 12, color: "crimson" }}>{error}</p>}

      <div style={{ marginTop: 12, display: "grid", gap: 12 }}>
        {items.map((item) => {
          const author = item?.external_author || "Feed";
          const url = item?.original_url || "#";
          const content = item?.content || "";
          const tags = Array.isArray(item?.tags) ? item.tags : [];
          const published = item?.published_at ? new Date(item.published_at).toLocaleString() : "";
          const platformLabel = item?.source_platform || "";

          return (
            <div key={item.id} style={{ border: "1px solid #eee", borderRadius: 14, padding: 12 }}>
              <div style={{ display: "flex", gap: 8, justifyContent: "space-between", alignItems: "baseline" }}>
                <div style={{ fontWeight: 800 }}>{author}</div>
                <div style={{ opacity: 0.6, fontSize: 12, textAlign: "right" }}>
                  {published}
                  {platformLabel ? <span style={{ marginLeft: 8 }}>({platformLabel})</span> : null}
                </div>
              </div>

              {content ? (
                <div style={{ marginTop: 8, whiteSpace: "pre-wrap", lineHeight: 1.5 }}>{content}</div>
              ) : null}

              {item?.embed_html ? (
                <div
                  style={{ marginTop: 10 }}
                  // Adminが登録したHTMLのみを表示する想定（デモ用途）
                  dangerouslySetInnerHTML={{ __html: item.embed_html }}
                />
              ) : (
                <a href={url} target="_blank" rel="noreferrer" style={{ display: "inline-block", marginTop: 10 }}>
                  Open original post
                </a>
              )}

              {tags.length > 0 ? (
                <div style={{ display: "flex", gap: 6, flexWrap: "wrap", marginTop: 10 }}>
                  {tags.map((t) => (
                    <span key={t} style={{ padding: "3px 8px", borderRadius: 999, background: "#f3f4f6", fontSize: 12 }}>
                      {t}
                    </span>
                  ))}
                </div>
              ) : null}
            </div>
          );
        })}
      </div>

      <div style={{ display: "flex", gap: 10, justifyContent: "center", marginTop: 14, alignItems: "center" }}>
        <button disabled={!canPrev} onClick={() => setPage((p) => Math.max(1, p - 1))}>
          Prev
        </button>

        <div style={{ opacity: 0.7 }}>
          {meta?.page ?? 1} / {meta?.totalPages ?? 1}
        </div>

        <button disabled={!canNext} onClick={() => setPage((p) => p + 1)}>
          Next
        </button>
      </div>
    </div>
  );
}
