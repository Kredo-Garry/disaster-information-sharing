// frontend/src/builder/setup.js
import { Builder, builder } from "@builder.io/react";
import GoogleMapBlock from "../components/GoogleMapBlock";
import HomeEarthquakeBlock from "../components/HomeEarthquakeBlock";
import HomeTsunamiBlock from "../components/HomeTsunamiBlock";
import HomeVolcanoBlock from "../components/HomeVolcanoBlock";
import CurrentLocationTextBlock from "../components/CurrentLocationTextBlock";
import MyPageBlock from "../components/MyPageBlock";
import FeedBlock from "../components/FeedBlock"; // ✅ 追加

/**
 * Builder の init / register を「このファイル1箇所」に集約する。
 * CRA(ブラウザ)前提で window にフラグを立てて二重実行を防ぐ。
 */
const w = typeof window !== "undefined" ? window : null;

// init（1回だけ）
if (w && !w.__BUILDER_INIT_DONE__) {
  const apiKey = process.env.REACT_APP_BUILDER_API_KEY;

  if (!apiKey) {
    throw new Error(
      "REACT_APP_BUILDER_API_KEY is not set. Create frontend/.env and set REACT_APP_BUILDER_API_KEY."
    );
  }

  builder.init(apiKey);
  w.__BUILDER_INIT_DONE__ = true;
}

// registerComponent（1回だけ）
if (w && !w.__BUILDER_REGISTRY_DONE__) {
  console.log("✅ builder-registry loaded");

  // --- GoogleMapBlock ---
  Builder.registerComponent(GoogleMapBlock, {
    name: "GoogleMapBlock",
    inputs: [
      { name: "apiKey", type: "string", helperText: "未指定なら .env を使用" },
      { name: "apiBaseUrl", type: "string", defaultValue: "http://localhost:8000" },
      { name: "height", type: "number", defaultValue: 420 },
      { name: "zoom", type: "number", defaultValue: 12 },
      { name: "centerLat", type: "number", defaultValue: 10.3157 },
      { name: "centerLng", type: "number", defaultValue: 123.8854 },
    ],
  });

  // --- CurrentLocationTextBlock ---
  Builder.registerComponent(CurrentLocationTextBlock, {
    name: "CurrentLocationTextBlock",
    inputs: [
      { name: "title", type: "string", defaultValue: "Current Location" },
      { name: "fallbackText", type: "string", defaultValue: "Detecting location..." },
      { name: "deniedText", type: "string", defaultValue: "Location permission denied" },
      { name: "errorText", type: "string", defaultValue: "Location unavailable" },
      { name: "cacheMinutes", type: "number", defaultValue: 10 },
      { name: "format", type: "string", defaultValue: "{city}, {region}" },
      { name: "icon", type: "boolean", defaultValue: true },
      { name: "iconScale", type: "number", defaultValue: 1.3 },
      { name: "valueScale", type: "number", defaultValue: 1.3 },
      { name: "className", type: "string", defaultValue: "" },
      { name: "titleClassName", type: "string", defaultValue: "" },
      { name: "valueClassName", type: "string", defaultValue: "" },
    ],
  });

  // --- HomeEarthquakeBlock ---
  Builder.registerComponent(HomeEarthquakeBlock, {
    name: "HomeEarthquakeBlock",
    inputs: [
      { name: "title", type: "string", defaultValue: "Latest PHIVOLCS Earthquakes" },
      { name: "limit", type: "number", defaultValue: 10 },
      { name: "apiBaseUrl", type: "string", defaultValue: "" },
    ],
  });

  // --- HomeTsunamiBlock ---
  Builder.registerComponent(HomeTsunamiBlock, {
    name: "HomeTsunamiBlock",
    inputs: [
      { name: "title", type: "string", defaultValue: "Latest PHIVOLCS Tsunami Bulletins" },
      { name: "limit", type: "number", defaultValue: 3 },
      { name: "apiBaseUrl", type: "string", defaultValue: "" },
    ],
  });

  // --- HomeVolcanoBlock ---
  Builder.registerComponent(HomeVolcanoBlock, {
    name: "HomeVolcanoBlock",
    inputs: [
      { name: "title", type: "string", defaultValue: "Latest PHIVOLCS Volcano Bulletins" },
      { name: "limit", type: "number", defaultValue: 3 },
      { name: "apiBaseUrl", type: "string", defaultValue: "" },
    ],
  });

  // ✅ MyPageBlock (/my-page)
  Builder.registerComponent(MyPageBlock, {
    name: "MyPageBlock",
    inputs: [
      { name: "title", type: "string", defaultValue: "My Page" },
      { name: "apiBaseUrl", type: "string", defaultValue: "http://localhost:8000" },
    ],
  });

  // ✅ FeedBlock (/feed)
  Builder.registerComponent(FeedBlock, {
    name: "FeedBlock",
    inputs: [
      { name: "title", type: "string", defaultValue: "Disaster Feed" },
      { name: "apiBaseUrl", type: "string", defaultValue: "http://localhost:8000" },
      { name: "pageSize", type: "number", defaultValue: 10 },
      { name: "defaultTag", type: "string", defaultValue: "" },
      { name: "defaultPlatform", type: "string", defaultValue: "" },
    ],
  });

  w.__BUILDER_REGISTRY_DONE__ = true;
}

export {};
