import { BuilderComponent, builder } from "@builder.io/react";
import { useEffect, useState } from "react";

export default function BuilderPage() {
  const [content, setContent] = useState(null);

  useEffect(() => {
    builder
      .get("page", {
        url: window.location.pathname,
      })
      .then((res) => {
        console.log("Builder response:", res);
        setContent(res); // ← ここ重要！
      });
  }, []);

  if (!content) return <div>Loading...</div>;

  return <BuilderComponent model="page" content={content} />;
}
