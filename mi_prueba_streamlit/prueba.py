from pathlib import Path

import numpy as np
import pandas as pd
import streamlit as st

BASE_DIR = Path(__file__).resolve().parent
HTML_PATH = BASE_DIR / "PaginaWeb" / "dev" / "index.html"

st.set_page_config(page_title="Mi App de Robot", page_icon="🤖", layout="centered")
st.title("🤖 Panel de Control - Proyecto de Robótica")
st.write("Bienvenido, Emmanuel. Esta aplicación corre en tu entorno virtual de Linux virtual (WSL).")

st.subheader("Configuración de Motores")
velocidad = st.slider("Ajustar velocidad del motor (RPM)", 0, 200, 50)
modo = st.selectbox("Seleccionar modo de operación", ["Autónomo", "Manual", "Espera"])

if st.button("Enviar Comando al Robot"):
    st.success(f"¡Comando enviado con éxito! Modo: **{modo}** | Velocidad: **{velocidad} RPM**")

st.subheader("Monitoreo de Sensores")
chart_data = pd.DataFrame(np.random.randn(20, 2), columns=["Sensor Izquierdo", "Sensor Derecho"])
st.line_chart(chart_data)

st.subheader("Vista del Sitio Web")
if HTML_PATH.exists():
    html_content = HTML_PATH.read_text(encoding="utf-8")
    st.components.v1.html(html_content, height=900, scrolling=True)
else:
    st.warning(f"No se encontró la página HTML en: {HTML_PATH}")
