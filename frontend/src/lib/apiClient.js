import axios from "axios";

/**
 * Laravel(web middleware) のCSRF cookie(XSRF-TOKEN)を取得する。
 * Sanctumを入れてないので /login を叩いて cookie をもらうのが一番手軽。
 */
async function ensureCsrfCookie(apiBaseUrl) {
  const url = `${apiBaseUrl}/login`;

  // すでにXSRF-TOKENがあれば再取得しない
  const hasXsrf = document.cookie.split("; ").some((c) => c.startsWith("XSRF-TOKEN="));
  if (hasXsrf) return;

  await axios.get(url, {
    withCredentials: true,
  });
}

/**
 * MyPage用APIクライアント
 */
export function createApiClient(apiBaseUrl) {
  const client = axios.create({
    baseURL: apiBaseUrl,
    withCredentials: true,
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
  });

  client.ensureCsrf = () => ensureCsrfCookie(apiBaseUrl);

  return client;
}
