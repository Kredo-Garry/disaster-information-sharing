// GoogleMapBlock.jsx
import { GoogleMap, LoadScript } from "@react-google-maps/api";
import { useEffect, useMemo, useRef } from "react";

export default function GoogleMapBlock(props) {
  const {
    apiKey,
    height = "100%",   // Builder 側で 100vh とか入れるなら string で受けるのが楽
    width = "100%",
    zoom = 12,
    centerLat = 10.3157,
    centerLng = 123.8854,
  } = props;

  const mapRef = useRef(null);

  const containerStyle = useMemo(
    () => ({
      width,
      height,     // ←ここが超重要
      minHeight: "200px",
    }),
    [width, height]
  );

  // 高さが変わったときに地図が崩れるのを防ぐ（任意だけど効く）
  useEffect(() => {
    if (!mapRef.current) return;
    window.google?.maps?.event?.trigger(mapRef.current, "resize");
  }, [height]);

  return (
    <div style={{ width: "100%", height: "100%" }}>
      <LoadScript googleMapsApiKey={apiKey || import.meta.env.VITE_GOOGLE_MAPS_API_KEY}>
        <GoogleMap
          mapContainerStyle={containerStyle}
          center={{ lat: centerLat, lng: centerLng }}
          zoom={zoom}
          onLoad={(map) => (mapRef.current = map)}
        />
      </LoadScript>
    </div>
  );
}
