import { BuilderComponent, builder } from '@builder.io/react';
import { useEffect, useState } from 'react';

builder.init("fd5613ef943248cba12bc759d9eea157");

export default function BuilderPage() {
  const [content, setContent] = useState(null);

  useEffect(() => {
    builder.get('page', { url: window.location.pathname })
      .promise()
      .then(setContent);
  }, []);

  return (
    <BuilderComponent model="page" content={content} />
  );
}
