import { BrowserRouter, Routes, Route } from 'react-router-dom';
import BuilderPage from './BuilderPage';

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/*" element={<BuilderPage />} />
      </Routes>
    </BrowserRouter>
  );
}
