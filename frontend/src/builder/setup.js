import { Builder, builder } from "@builder.io/react";
import GoogleMapBlock from "../components/GoogleMapBlock";

/**
 * Builder の init / register を「このファイル1箇所」に集約する。
 * CRA(ブラウザ)前提で window にフラグを立てて二重実行を防ぐ。
 */
const w = typeof window !== "undefined" ? window : null;

// init（1回だけ）
if (w && !w.__BUILDER_INIT_DONE__) {
  const apiKey = process.env.REACT_APP_BUILDER_API_KEY;

  if (!apiKey) {
    // fail-fast: 設定漏れにすぐ気づけるようにする
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

  Builder.registerComponent(GoogleMapBlock, {
    name: "GoogleMapBlock",
    inputs: [
      { name: "apiKey", type: "string", helperText: "未指定なら .env を使用" },
      { name: "height", type: "number", defaultValue: 420 },
      { name: "zoom", type: "number", defaultValue: 12 },
      { name: "centerLat", type: "number", defaultValue: 10.3157 },
      { name: "centerLng", type: "number", defaultValue: 123.8854 }
    ]
  });

  w.__BUILDER_REGISTRY_DONE__ = true;
}
