// GoogleMapBlock.jsx
import { GoogleMap, MarkerF, InfoWindowF, useJsApiLoader } from "@react-google-maps/api";
import { useCallback, useEffect, useMemo, useState } from "react";

// title（Select Alert Type）→ 絵文字
const emojiByTitle = (title) => {
  const t = String(title || "").toLowerCase();
  if (t.includes("heavy rain")) return "🌧️";
  if (t.includes("tsunami")) return "🌊";
  if (t.includes("road closure")) return "🚧";
  if (t.includes("fire")) return "🔥";
  if (t.includes("lightning")) return "⚡";
  if (t.includes("water outage")) return "🚰";
  if (t.includes("power outage")) return "💡";
  if (t.includes("unstable internet")) return "🛜";
  return "📍";
};

// 「存在するけど見えない」ピン（クリック判定用）
const invisiblePinIcon = {
  path: "M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z",
  fillColor: "#ff0000",
  fillOpacity: 0,
  strokeColor: "#ff0000",
  strokeOpacity: 0,
  scale: 1.5,
  anchor: { x: 12, y: 24 },
};

export default function GoogleMapBlock(props) {
  const {
    apiKey,
    height = "100%",
    width = "100%",
    zoom = 12,
    centerLat = 12.8797,
    centerLng = 121.7740,
    apiBaseUrl = "http://localhost:8000",
  } = props;

  const key = apiKey || process.env.REACT_APP_GOOGLE_MAPS_API_KEY;

  // ✅ loadError は使っていないので分割代入しない
  const { isLoaded } = useJsApiLoader({
    id: "diship-google-maps",
    googleMapsApiKey: key || "DUMMY",
  });

  const [center, setCenter] = useState({ lat: centerLat, lng: centerLng });
  const [currentLocation, setCurrentLocation] = useState(null);
  const [posts, setPosts] = useState([]);
  const [activePostId, setActivePostId] = useState(null);

  const containerStyle = useMemo(
    () => ({ width, height, minHeight: "300px" }),
    [width, height]
  );

  useEffect(() => {
    setCenter({ lat: centerLat, lng: centerLng });
  }, [centerLat, centerLng]);

  // 現在地取得
  useEffect(() => {
    if (!navigator.geolocation) return;

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        setCurrentLocation({ lat, lng });
        setCenter({ lat, lng });
      },
      () => {},
      { enableHighAccuracy: true, timeout: 10000 }
    );
  }, []);

  // 投稿一覧取得
  const fetchPosts = useCallback(async () => {
    const res = await fetch(`${apiBaseUrl}/api/posts`);
    const text = await res.text().catch(() => "");
    if (!res.ok) throw new Error(`Failed to fetch posts: ${res.status} ${text}`);

    const data = text ? JSON.parse(text) : [];
    const normalized = (Array.isArray(data) ? data : []).map((p) => ({
      ...p,
      lat: Number(p.lat),
      lng: Number(p.lng),
    }));

    setPosts(normalized);
    return normalized;
  }, [apiBaseUrl]);

  useEffect(() => {
    fetchPosts().catch(console.error);
  }, [fetchPosts]);

  // Builder → 投稿
  useEffect(() => {
    window.dishipCreatePost = async ({ title, body }) => {
      if (!currentLocation) throw new Error("Current location is not ready yet.");

      const payload = {
        title,
        body,
        lat: currentLocation.lat,
        lng: currentLocation.lng,
      };

      const res = await fetch(`${apiBaseUrl}/api/posts`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      const text = await res.text().catch(() => "");
      if (!res.ok) throw new Error(text);

      const created = text ? JSON.parse(text) : null;

      await fetchPosts();

      if (created?.lat && created?.lng) {
        setCenter({ lat: Number(created.lat), lng: Number(created.lng) });
      }
      if (created?.id != null) {
        setActivePostId(created.id);
      }

      return created;
    };

    return () => {
      delete window.dishipCreatePost;
    };
  }, [apiBaseUrl, currentLocation, fetchPosts]);

  if (!isLoaded) return <div>Loading map...</div>;

  const activePost = posts.find((p) => p.id === activePostId);

  return (
    <GoogleMap
      mapContainerStyle={containerStyle}
      center={center}
      zoom={zoom}
      onClick={() => setActivePostId(null)}
    >
      {/* 現在地ピン（赤） */}
      {currentLocation && <MarkerF position={currentLocation} />}

      {/* 投稿ピン：透明ピン + 絵文字 */}
      {posts
        .filter((p) => Number.isFinite(p.lat) && Number.isFinite(p.lng))
        .map((p) => (
          <MarkerF
            key={p.id}
            position={{ lat: p.lat, lng: p.lng }}
            icon={invisiblePinIcon}
            label={{
              text: emojiByTitle(p.title),
              fontSize: "22px",
            }}
            onClick={() => setActivePostId(p.id)}
          />
        ))}

      {/* 詳細表示 */}
      {activePost && (
        <InfoWindowF
          position={{ lat: activePost.lat, lng: activePost.lng }}
          onCloseClick={() => setActivePostId(null)}
        >
          <div style={{ maxWidth: 260 }}>
            <strong>
              {emojiByTitle(activePost.title)} {activePost.title}
            </strong>
            <div style={{ marginTop: 6, whiteSpace: "pre-wrap" }}>
              {activePost.body || "(No details)"}
            </div>
          </div>
        </InfoWindowF>
      )}
    </GoogleMap>
  );
}
