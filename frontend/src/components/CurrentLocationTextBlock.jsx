import React, { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { useJsApiLoader } from "@react-google-maps/api";

export default function CurrentLocationTextBlock(props) {
  const {
    title = "Current Location",
    fallbackText = "Detecting location...",
    deniedText = "Location permission denied",
    errorText = "Location unavailable",

    // 地名が取れたらそれを長めに使う（ユーザー体験優先）
    cacheMinutes = 60 * 24 * 7, // 7 days

    // 表示フォーマット
    format = "{city}, {region}",

    icon = true,
    iconScale = 1.3,
    valueScale = 1.3,
    className = "",
  } = props;

  const apiKey = process.env.REACT_APP_GOOGLE_MAPS_API_KEY;

  const { isLoaded } = useJsApiLoader({
    id: "diship-geocoder-loader",
    googleMapsApiKey: apiKey || "",
  });

  const [text, setText] = useState(fallbackText);
  const [coords, setCoords] = useState(null);

  // v5：過去キャッシュと完全分離
  const cacheKey = useMemo(() => "diship_current_location_text_v5", []);
  const cacheTtlMs = useMemo(
    () => Number(cacheMinutes) * 60 * 1000,
    [cacheMinutes]
  );

  // 地名が一度でも取れたら維持（失敗しても画面を壊さない）
  const lastGoodRef = useRef("");

  // eslint warning対策：useCallback で依存を安定化
  const formatPlace = useCallback(
    (results0) => {
      const comps = results0?.address_components || [];
      const pick = (type) =>
        comps.find((c) => (c.types || []).includes(type))?.long_name || "";

      // 「近い地名でOK」優先順
      const city =
        pick("locality") ||
        pick("administrative_area_level_2") ||
        pick("sublocality") ||
        pick("sublocality_level_1") ||
        "";
      const region = pick("administrative_area_level_1") || "";
      const country = pick("country") || "";

      const formatted = String(format)
        .replace("{city}", city)
        .replace("{region}", region)
        .replace("{country}", country)
        .replace(/,\s*,/g, ",")
        .replace(/^\s*,\s*/g, "")
        .replace(/\s*,\s*$/g, "")
        .trim();

      return formatted || "";
    },
    [format]
  );

  /**
   * ① 初期表示：キャッシュがあればそれを出す（TTL外でもOK）
   * ② 位置は「近い地名」でOKなので低精度・高速に
   */
  useEffect(() => {
    let canceled = false;
    const setSafe = (v) => !canceled && setText(v);

    // cache read
    try {
      const raw = localStorage.getItem(cacheKey);
      if (raw) {
        const parsed = JSON.parse(raw);
        const value = typeof parsed?.value === "string" ? parsed.value : "";
        const ts = parsed?.ts;

        if (value) {
          lastGoodRef.current = value;

          // TTL内なら即表示、TTL外でも表示は維持（UX優先）
          if (!ts || Date.now() - ts < cacheTtlMs) setSafe(value);
          else setSafe(value);
        }
      }
    } catch (_) {}

    if (!("geolocation" in navigator)) {
      if (!lastGoodRef.current) setSafe(errorText);
      return () => {
        canceled = true;
      };
    }

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        if (canceled) return;
        setCoords({
          lat: pos.coords.latitude,
          lng: pos.coords.longitude,
        });
      },
      (err) => {
        if (err?.code === 1) {
          setSafe(deniedText);
          return;
        }
        // 失敗しても lastGood があれば表示維持、なければ error
        if (!lastGoodRef.current) setSafe(errorText);
      },
      {
        enableHighAccuracy: false,
        timeout: 8000,
        maximumAge: 1000 * 60 * 60, // 1 hour
      }
    );

    return () => {
      canceled = true;
    };
  }, [cacheKey, cacheTtlMs, deniedText, errorText]);

  /**
   * ③ coords と Google が揃ったら reverse geocode
   * - 失敗しても lastGood を維持
   * - 成功したらキャッシュ更新
   */
  useEffect(() => {
    if (!coords) return;
    if (!isLoaded || !window.google?.maps?.Geocoder) return;

    let canceled = false;
    const geocoder = new window.google.maps.Geocoder();

    const geocodeWithRetry = (attempt) => {
      geocoder.geocode({ location: coords }, (results, status) => {
        if (canceled) return;

        if (status === "OK" && results?.length) {
          const name = formatPlace(results[0]);
          if (name) {
            setText(name);
            lastGoodRef.current = name;
            try {
              localStorage.setItem(
                cacheKey,
                JSON.stringify({ value: name, ts: Date.now() })
              );
            } catch (_) {}
            return;
          }
        }

        // consoleにだけ残す（ユーザー画面は汚さない）
        console.warn("[DIShiP] reverse geocode failed:", status);

        if (attempt < 3) {
          setTimeout(() => geocodeWithRetry(attempt + 1), 500 * (attempt + 1));
          return;
        }

        if (!lastGoodRef.current) setText(errorText);
      });
    };

    geocodeWithRetry(0);

    return () => {
      canceled = true;
    };
  }, [coords, isLoaded, cacheKey, errorText, formatPlace]);

  const iconBox = Math.round(44 * iconScale);
  const iconRadius = Math.round(12 * iconScale);
  const svgSize = Math.round(18 * iconScale);
  const valuePx = Math.round(24 * valueScale);

  return (
    <div className={className} style={{ display: "flex", alignItems: "center", gap: 12 }}>
      {icon && (
        <div
          style={{
            width: iconBox,
            height: iconBox,
            borderRadius: iconRadius,
            background: "#EEF6FF",
            display: "grid",
            placeItems: "center",
            flex: "0 0 auto",
          }}
        >
          <svg width={svgSize} height={svgSize} viewBox="0 0 24 24" fill="none">
            <path
              d="M12 22s7-4.5 7-12a7 7 0 1 0-14 0c0 7.5 7 12 7 12Z"
              stroke="#2563EB"
              strokeWidth="2"
            />
            <circle cx="12" cy="10" r="2.5" stroke="#2563EB" strokeWidth="2" />
          </svg>
        </div>
      )}

      <div style={{ minWidth: 0 }}>
        <div style={{ color: "#6B7280", fontSize: 14, fontWeight: 500 }}>
          {title}
        </div>
        <div
          style={{
            fontSize: valuePx,
            fontWeight: 700,
            color: "#111827",
            whiteSpace: "nowrap",
            overflow: "hidden",
            textOverflow: "ellipsis",
            maxWidth: "100%",
          }}
          title={text}
        >
          {text}
        </div>
      </div>
    </div>
  );
}
