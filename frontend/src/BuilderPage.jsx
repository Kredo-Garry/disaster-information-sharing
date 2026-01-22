import { BuilderComponent } from "@builder.io/react";

export default function BuilderPage() {
  return <BuilderComponent model="page" options={{ includeRefs: true }} />;
}
