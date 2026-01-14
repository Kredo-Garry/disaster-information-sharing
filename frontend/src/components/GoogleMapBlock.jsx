import React from "react";
import { GoogleMap, LoadScript } from "@react-google-maps/api";

export default function GoogleMapBlock({
  apiKey,
  height = 420,
  zoom = 12,
  centerLat = 10.3157,
  centerLng = 123.8854,
}) {
  const key = apiKey || process.env.REACT_APP_GOOGLE_MAPS_API_KEY;

  if (!key) {
    return (
      <div style={{ padding: 12, border: "1px solid #ddd" }}>
        Google Maps API Key が未設定です。<br />
        .env に REACT_APP_GOOGLE_MAPS_API_KEY を設定するか、Builder 側の props に apiKey を渡してください。
      </div>
    );
  }

  const center = { lat: Number(centerLat), lng: Number(centerLng) };

  return (
    <LoadScript googleMapsApiKey={key}>
      <GoogleMap
        mapContainerStyle={{ width: "100%", height: Number(height) }}
        center={center}
        zoom={Number(zoom)}
        options={{
          clickableIcons: false,
          fullscreenControl: true,
          streetViewControl: false,
          mapTypeControl: false,
        }}
      />
    </LoadScript>
  );
}
