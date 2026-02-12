import React, { useEffect, useMemo, useState } from "react";
import { createApiClient } from "../lib/apiClient";

function timeAgo(iso) {
  if (!iso) return null;
  const t = new Date(iso).getTime();
  const diff = Date.now() - t;
  const sec = Math.floor(diff / 1000);
  if (sec < 60) return `${sec}s ago`;
  const min = Math.floor(sec / 60);
  if (min < 60) return `${min}m ago`;
  const hr = Math.floor(min / 60);
  if (hr < 24) return `${hr}h ago`;
  const day = Math.floor(hr / 24);
  return `${day}d ago`;
}

function StatusPill({ status }) {
  if (status === "help") {
    return (
      <span style={{ display: "inline-flex", alignItems: "center", gap: 6, padding: "4px 10px", borderRadius: 999, fontSize: 12, background: "#FDE2E2", color: "#B42318" }}>
        ▲ Help
      </span>
    );
  }
  if (status === "safe") {
    return (
      <span style={{ display: "inline-flex", alignItems: "center", gap: 6, padding: "4px 10px", borderRadius: 999, fontSize: 12, background: "#DCFCE7", color: "#166534" }}>
        ✓ Safe
      </span>
    );
  }
  // neutral（表示はお任せ仕様）
  return (
    <span style={{ display: "inline-flex", alignItems: "center", gap: 6, padding: "4px 10px", borderRadius: 999, fontSize: 12, background: "#F2F4F7", color: "#344054" }}>
      • Neutral
    </span>
  );
}

export default function MyPageBlock({
  apiBaseUrl = "http://localhost:8000",
  title = "My Page",
}) {
  const api = useMemo(() => createApiClient(apiBaseUrl), [apiBaseUrl]);

  const [me, setMe] = useState(null);
  const [members, setMembers] = useState([]);
  const [notice, setNotice] = useState(null);

  const [familyIdInput, setFamilyIdInput] = useState("");

  const [helpOpen, setHelpOpen] = useState(false);
  const [helpMessage, setHelpMessage] = useState("");
  const [loading, setLoading] = useState(false);
  const [err, setErr] = useState(null);

  async function loadAll() {
    setErr(null);
    setLoading(true);
    try {
      // web.php配下のPATCHに備えてCSRF cookieを先に確保
      await api.ensureCsrf();

      const meRes = await api.get("/api/me");
      setMe(meRes.data.user);

      const famRes = await api.get("/api/family");
      setMembers(famRes.data.members || []);
      setNotice(famRes.data.notice || null);

      // family_id入力欄へ反映
      const fid = meRes.data.user?.family_id || "";
      setFamilyIdInput(fid);
    } catch (e) {
      setErr(e?.response?.data?.message || e.message || "Failed to load");
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadAll();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [apiBaseUrl]);

  async function setSafe() {
    setErr(null);
    setLoading(true);
    try {
      await api.ensureCsrf();
      const res = await api.patch("/api/me/status", { status: "safe" });
      setMe(res.data.user);

      const famRes = await api.get("/api/family");
      setMembers(famRes.data.members || []);
      setNotice(famRes.data.notice || null);
    } catch (e) {
      setErr(e?.response?.data?.message || e.message || "Failed to update status");
    } finally {
      setLoading(false);
    }
  }

  async function sendHelp() {
    setErr(null);
    setLoading(true);
    try {
      await api.ensureCsrf();
      const res = await api.patch("/api/me/status", { status: "help", message: helpMessage });
      setMe(res.data.user);

      const famRes = await api.get("/api/family");
      setMembers(famRes.data.members || []);
      setNotice(famRes.data.notice || null);

      setHelpOpen(false);
      setHelpMessage("");
    } catch (e) {
      setErr(e?.response?.data?.message || e.message || "Failed to send help request");
    } finally {
      setLoading(false);
    }
  }

  async function registerFamilyId() {
    setErr(null);
    setLoading(true);
    try {
      await api.ensureCsrf();
      const res = await api.patch("/api/me/family", { family_id: familyIdInput });
      setMe(res.data.user);

      const famRes = await api.get("/api/family");
      setMembers(famRes.data.members || []);
      setNotice(famRes.data.notice || null);
    } catch (e) {
      setErr(e?.response?.data?.message || e.message || "Failed to register Family ID");
    } finally {
      setLoading(false);
    }
  }

  const myFamilyId = me?.family_id || "-";
  const myStatus = me?.status || "neutral";

  return (
    <div style={{ maxWidth: 980, margin: "0 auto", padding: 24 }}>
      <h2 style={{ margin: "6px 0 18px", fontSize: 22 }}>{title}</h2>

      {err && (
        <div style={{ marginBottom: 12, padding: 12, borderRadius: 8, background: "#FEE4E2", color: "#7A271A" }}>
          {err}
        </div>
      )}

      <div style={{ display: "flex", alignItems: "center", gap: 16, padding: 18, borderRadius: 14, border: "1px solid #EAECF0", background: "#fff" }}>
        <div style={{ width: 64, height: 64, borderRadius: "50%", background: "#EEF2FF", display: "grid", placeItems: "center", fontSize: 28 }}>
          👤
        </div>
        <div style={{ flex: 1 }}>
          <div style={{ fontSize: 22, fontWeight: 700, lineHeight: 1.2 }}>{me?.name || "Loading..."}</div>
          <div style={{ marginTop: 6, fontSize: 13, color: "#667085" }}>Family ID : {myFamilyId}</div>
          <div style={{ marginTop: 10 }}>
            <StatusPill status={myStatus} />
          </div>
        </div>

        <div style={{ display: "flex", gap: 10 }}>
          <button
            onClick={setSafe}
            disabled={loading}
            style={{
              minWidth: 160,
              height: 44,
              borderRadius: 10,
              border: "1px solid #12B76A",
              background: "#12B76A",
              color: "#fff",
              fontWeight: 700,
              cursor: "pointer",
              opacity: loading ? 0.6 : 1,
            }}
          >
            ✓ I&apos;m Safe
          </button>

          <button
            onClick={() => setHelpOpen(true)}
            disabled={loading}
            style={{
              minWidth: 160,
              height: 44,
              borderRadius: 10,
              border: "1px solid #FDA29B",
              background: "#FDA29B",
              color: "#fff",
              fontWeight: 700,
              cursor: "pointer",
              opacity: loading ? 0.6 : 1,
            }}
          >
            ⓘ Help
          </button>
        </div>
      </div>

      {/* Family */}
      <div style={{ marginTop: 22 }}>
        <div style={{ fontSize: 18, fontWeight: 800, marginBottom: 12 }}>Family</div>

        {(notice || members.length === 0) ? (
          <div style={{ padding: 14, borderRadius: 12, border: "1px solid #EAECF0", background: "#fff", color: "#475467" }}>
            {notice || "No Family ID is registered, or no users with the same Family ID were found."}
          </div>
        ) : (
          <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(220px, 1fr))", gap: 14 }}>
            {members.map((m) => {
              const ago = timeAgo(m.status_updated_at); // neutralならnull → 非表示
              return (
                <div key={m.id} style={{ borderRadius: 14, border: "1px solid #EAECF0", background: "#fff", padding: 16 }}>
                  <div style={{ width: 48, height: 48, borderRadius: "50%", background: "#EEF2FF", display: "grid", placeItems: "center", fontSize: 22, margin: "0 auto 10px" }}>
                    👤
                  </div>

                  <div style={{ textAlign: "center", fontWeight: 800 }}>{m.name}</div>

                  <div style={{ marginTop: 8, display: "flex", justifyContent: "center" }}>
                    <StatusPill status={m.status} />
                  </div>

                  {m.status === "help" && m.status_message ? (
                    <div style={{ marginTop: 10, padding: 10, borderRadius: 10, background: "#FDECEC", border: "1px solid #FDA29B", color: "#7A271A", fontSize: 13 }}>
                      {m.status_message}
                    </div>
                  ) : null}

                  {ago ? (
                    <div style={{ marginTop: 10, textAlign: "center", fontSize: 12, color: "#667085" }}>
                      {ago}
                    </div>
                  ) : null}
                </div>
              );
            })}
          </div>
        )}
      </div>

      {/* Family ID register */}
      <div style={{ marginTop: 28, padding: 18, borderRadius: 14, border: "1px solid #EAECF0", background: "#fff" }}>
        <div style={{ fontSize: 18, fontWeight: 900, marginBottom: 10 }}>Register Family ID</div>
        <div style={{ fontSize: 13, color: "#667085", marginBottom: 14 }}>
          Enter a Family ID to show users with the same Family ID in the Family section.
        </div>

        <div style={{ display: "flex", gap: 10, alignItems: "center" }}>
          <input
            value={familyIdInput}
            onChange={(e) => setFamilyIdInput(e.target.value)}
            placeholder="e.g. ABC123"
            style={{
              flex: 1,
              height: 42,
              borderRadius: 10,
              border: "1px solid #D0D5DD",
              padding: "0 12px",
              outline: "none",
            }}
          />
          <button
            onClick={registerFamilyId}
            disabled={loading || !familyIdInput}
            style={{
              minWidth: 140,
              height: 42,
              borderRadius: 10,
              border: "1px solid #84CAFF",
              background: "#84CAFF",
              color: "#0B4A6F",
              fontWeight: 800,
              cursor: "pointer",
              opacity: (loading || !familyIdInput) ? 0.6 : 1,
            }}
          >
            Register
          </button>
        </div>
      </div>

      {/* Help modal */}
      {helpOpen && (
        <div
          style={{
            position: "fixed",
            inset: 0,
            background: "rgba(0,0,0,0.45)",
            display: "grid",
            placeItems: "center",
            padding: 20,
            zIndex: 1000,
          }}
          onClick={() => setHelpOpen(false)}
        >
          <div
            style={{ width: "min(700px, 100%)", borderRadius: 14, background: "#fff", border: "1px solid #EAECF0", padding: 18 }}
            onClick={(e) => e.stopPropagation()}
          >
            <div style={{ fontSize: 20, fontWeight: 900 }}>Help Request</div>
            <div style={{ marginTop: 6, fontSize: 13, color: "#667085" }}>
              Describe your situation to alert your followers
            </div>

            <textarea
              value={helpMessage}
              onChange={(e) => setHelpMessage(e.target.value)}
              placeholder="What help do you need?"
              rows={5}
              style={{
                width: "100%",
                marginTop: 14,
                borderRadius: 10,
                border: "1px solid #D0D5DD",
                padding: 12,
                outline: "none",
                resize: "vertical",
              }}
            />

            <div style={{ display: "flex", justifyContent: "flex-end", gap: 10, marginTop: 14 }}>
              <button
                onClick={() => setHelpOpen(false)}
                disabled={loading}
                style={{
                  height: 40,
                  padding: "0 16px",
                  borderRadius: 10,
                  border: "1px solid #D0D5DD",
                  background: "#fff",
                  cursor: "pointer",
                  opacity: loading ? 0.6 : 1,
                }}
              >
                Cancel
              </button>

              <button
                onClick={sendHelp}
                disabled={loading || !helpMessage.trim()}
                style={{
                  height: 40,
                  padding: "0 16px",
                  borderRadius: 10,
                  border: "1px solid #D92D20",
                  background: "#D92D20",
                  color: "#fff",
                  fontWeight: 800,
                  cursor: "pointer",
                  opacity: (loading || !helpMessage.trim()) ? 0.6 : 1,
                }}
              >
                Send Help Request
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
