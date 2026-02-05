import { Builder, builder } from "@builder.io/react";
import GoogleMapBlock from "../components/GoogleMapBlock";
import HomeEarthquakeBlock from "../components/HomeEarthquakeBlock";
import HomeTsunamiBlock from "../components/HomeTsunamiBlock";
import HomeVolcanoBlock from "../components/HomeVolcanoBlock";

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

  // --- HomeEarthquakeBlock ---
  Builder.registerComponent(HomeEarthquakeBlock, {
    name: "HomeEarthquakeBlock",
    inputs: [
      {
        name: "title",
        type: "string",
        defaultValue: "Latest PHIVOLCS Earthquakes",
        friendlyName: "Title",
      },
      {
        name: "limit",
        type: "number",
        defaultValue: 10,
        friendlyName: "Number of items",
        helperText: "How many earthquakes to show",
      },
      {
        name: "apiBaseUrl",
        type: "string",
        defaultValue: "",
        friendlyName: "API Base URL",
        helperText:
          'Leave blank if same origin. If React is :3000 and Laravel is :8000, set "http://localhost:8000".',
      },
    ],
  });

  // --- HomeTsunamiBlock ---
  Builder.registerComponent(HomeTsunamiBlock, {
    name: "HomeTsunamiBlock",
    inputs: [
      {
        name: "title",
        type: "string",
        defaultValue: "Latest PHIVOLCS Tsunami Bulletins",
        friendlyName: "Title",
      },
      {
        name: "limit",
        type: "number",
        defaultValue: 3,
        friendlyName: "Number of items",
      },
      {
        name: "apiBaseUrl",
        type: "string",
        defaultValue: "",
        friendlyName: "API Base URL",
        helperText:
          'Leave blank if same origin. If React is :3000 and Laravel is :8000, set "http://localhost:8000".',
      },
    ],
  });

  // --- HomeVolcanoBlock ---
  Builder.registerComponent(HomeVolcanoBlock, {
    name: "HomeVolcanoBlock",
    inputs: [
      {
        name: "title",
        type: "string",
        defaultValue: "Latest PHIVOLCS Volcano Bulletins",
        friendlyName: "Title",
      },
      {
        name: "limit",
        type: "number",
        defaultValue: 3,
        friendlyName: "Number of items",
      },
      {
        name: "apiBaseUrl",
        type: "string",
        defaultValue: "",
        friendlyName: "API Base URL",
        helperText:
          'Leave blank if same origin. If React is :3000 and Laravel is :8000, set "http://localhost:8000".',
      },
    ],
  });

  w.__BUILDER_REGISTRY_DONE__ = true;
}

export {};
