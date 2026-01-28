import "./builder/setup";
import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import BuilderPage from "./BuilderPage";

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        {/* 最初だけ / を /home に飛ばす（任意） */}
        <Route path="/" element={<Navigate to="/home" replace />} />

        {/* ここが肝：全部 BuilderPage に任せる */}
        <Route path="/*" element={<BuilderPage />} />
      </Routes>
    </BrowserRouter>
  );
}
