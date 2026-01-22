import "./builder/setup";
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import BuilderPage from './BuilderPage';

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        {/* ✅ 最初だけ / を /home に飛ばす */}
        <Route path="/" element={<Navigate to="/home" replace />} />

        {/* ✅ Builderのページは “それぞれのURL” で表示 */}
        <Route path="/home" element={<BuilderPage />} />
        <Route path="/map" element={<BuilderPage />} />
        <Route path="/feed" element={<BuilderPage />} />
        <Route path="/my-page" element={<BuilderPage />} />

        {/* （任意）存在しないURLは /home に戻す */}
        <Route path="*" element={<Navigate to="/home" replace />} />
      </Routes>
    </BrowserRouter>
  );
}
