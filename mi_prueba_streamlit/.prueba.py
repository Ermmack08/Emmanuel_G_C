from pathlib import Path

import streamlit as st

BASE_DIR = Path(__file__).resolve().parent
HTML_PATH = BASE_DIR / "PaginaWeb" / "dev" / "index.html"

st.set_page_config(page_title="Mi Página Web", layout="wide")
st.title("Proyecto Web")

if not HTML_PATH.exists():
    st.error(f"No se encontró el archivo HTML: {HTML_PATH}")
    st.stop()

html_content = HTML_PATH.read_text(encoding="utf-8")
st.components.v1.html(html_content, height=900, scrolling=True)
