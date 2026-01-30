// GoogleMapBlock.jsx
import { GoogleMap, MarkerF, useJsApiLoader } from "@react-google-maps/api";
import { useEffect, useMemo, useRef, useState } from "react";

export default function GoogleMapBlock(props) {
  console.log("MAP KEY:", props?.apiKey, process.env.REACT_APP_GOOGLE_MAPS_API_KEY);

  const {
    apiKey,
    height = "100%",
    width = "100%",
    zoom = 12,

    // 現在地取得前の仮センター（フィリピン）
    centerLat = 12.8797,
    centerLng = 121.7740,

    // Laravel API のベースURL（必要なら Builder から差し替え可）
    apiBaseUrl = "http://localhost:8000",
  } = props;

  const key = apiKey || process.env.REACT_APP_GOOGLE_MAPS_API_KEY;

  // ✅ LoadScript の代わりに useJsApiLoader（Builder/iframe環境で安定しやすい）
  const { isLoaded, loadError } = useJsApiLoader({
    id: "diship-google-maps",
    googleMapsApiKey: key || "DUMMY",
  });

  const mapRef = useRef(null);

  const [center, setCenter] = useState({ lat: centerLat, lng: centerLng });
  const [currentLocation, setCurrentLocation] = useState(null);

  const containerStyle = useMemo(
    () => ({
      width,
      height,
      minHeight: "300px", // 少し高めにして「親がautoで潰れる」事故を減らす
    }),
    [width, height]
  );

  // props の centerLat/centerLng が変わったら追随（Builderから変えた時など）
  useEffect(() => {
    setCenter({ lat: centerLat, lng: centerLng });
  }, [centerLat, centerLng]);

  // 現在地取得 → center 更新（拒否/失敗でも落とさない）
  useEffect(() => {
    if (!navigator.geolocation) {
      console.warn("Geolocation is not supported");
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;

        setCurrentLocation({ lat, lng });
        setCenter({ lat, lng });
      },
      (err) => {
        // ✅ ここ重要：拒否されても「落とさない」
        console.warn("Geolocation failed:", err);
        // setCenter は触らない（＝初期centerのまま）
      },
      { enableHighAccuracy: true, timeout: 10000 }
    );
  }, []);

  // Builder の高さ変更対策（任意）
  useEffect(() => {
    if (!mapRef.current) return;
    // isLoaded 前に触っても意味がないのでガード
    if (!window.google?.maps?.event) return;
    window.google.maps.event.trigger(mapRef.current, "resize");
  }, [height, isLoaded]);

  // Builder モーダルから呼べる投稿関数を window に生やす
  // Builder 側：window.dishipCreatePost({ title, body, category_id, user_id })
  useEffect(() => {
    window.dishipCreatePost = async ({ title, body, category_id, user_id }) => {
      if (!currentLocation) {
        throw new Error("Current location is not ready yet.");
      }

      const payload = {
        title,
        body,
        category_id: category_id ?? null,
        user_id: user_id ?? null,
        lat: currentLocation.lat,
        lng: currentLocation.lng,
      };

      const res = await fetch(`${apiBaseUrl}/api/posts`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      if (!res.ok) {
        const text = await res.text().catch(() => "");
        throw new Error(`Failed to create post: ${res.status} ${text}`);
      }

      const created = await res.json();

      // 投稿した場所へ地図を寄せる（任意）
      if (typeof created.lat === "number" && typeof created.lng === "number") {
        setCenter({ lat: created.lat, lng: created.lng });
      }

      return created;
    };

    return () => {
      delete window.dishipCreatePost;
    };
  }, [currentLocation, apiBaseUrl]);

  // ✅ ここから「真っ白」を避けるためのガード表示
  if (!key) {
    return <div style={{ padding: 12 }}>Google Maps API key is missing.</div>;
  }
  if (loadError) {
    return (
      <div style={{ padding: 12 }}>
        Failed to load Google Maps: {String(loadError)}
      </div>
    );
  }
  if (!isLoaded) {
    return <div style={{ padding: 12 }}>Loading map...</div>;
  }

  return (
    <div style={{ width: "100%", height: "100%" }}>
      <GoogleMap
        mapContainerStyle={containerStyle}
        center={center}
        zoom={zoom}
        onLoad={(map) => (mapRef.current = map)}
      >
        {/* 現在地ピン（任意） */}
        {currentLocation && <MarkerF position={currentLocation} />}
      </GoogleMap>
    </div>
  );
}
