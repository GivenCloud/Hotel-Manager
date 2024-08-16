/// <reference types="vite/client" />

interface ImportMetaEnv {
    readonly VITE_BASE_URL: string;
    // Otras variables de entorno que estés utilizando
  }
  
  interface ImportMeta {
    readonly env: ImportMetaEnv;
  }