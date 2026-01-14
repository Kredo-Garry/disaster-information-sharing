import { Builder } from "@builder.io/react";
import GoogleMapBlock from "./components/GoogleMapBlock";

console.log("✅ builder-registry loaded");

Builder.registerComponent(GoogleMapBlock, {
  name: "GoogleMapBlock",
  inputs: [
    { name: "apiKey", type: "string", helperText: "未指定なら .env を使用" },
    { name: "height", type: "number", defaultValue: 420 },
    { name: "zoom", type: "number", defaultValue: 12 },
    { name: "centerLat", type: "number", defaultValue: 10.3157 },
    { name: "centerLng", type: "number", defaultValue: 123.8854 },
  ],
});
