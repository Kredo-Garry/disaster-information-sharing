// src/components/builder/HomeEarthquakeBlock.builder.jsx
import React from "react";
import { Builder } from "@builder.io/react";
import HomeEarthquakeBlock from "../HomeEarthquakeBlock";

/**
 * Builder block registration
 *
 * - Builder上で「HomeEarthquakeBlock」として追加できるようにする
 * - apiBaseUrl / limit / title をBuilderの入力欄で変更可能
 */
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
        'Leave blank if React and Laravel are same origin. If React is :3000 and Laravel is :8000, set "http://localhost:8000".',
    },
  ],
});
