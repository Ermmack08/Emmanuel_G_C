import streamlit as st

# Configuración de la página web
st.set_page_config(page_title="Mi App de Robot", page_icon="🤖", layout="centered")

# Título principal de tu servidor local
st.title("🤖 Panel de Control - Proyecto de Robótica")
st.write("Bienvenido, Emmanuel. Esta aplicación corre en tu entorno virtual de Linux virtual (WSL).")

# Sección interactiva con widgets (principio de Streamlit)
st.subheader("Configuración de Motores")
velocidad = st.slider("Ajustar velocidad del motor (RPM)", 0, 200, 50)
modo = st.selectbox("Seleccionar modo de operación", ["Autónomo", "Manual", "Espera"])

# Botón y lógica de respuesta en tiempo real
if st.button("Enviar Comando al Robot"):
    st.success(f"¡Comando enviado con éxito! Modo: **{modo}** | Velocidad: **{velocidad} RPM**")

# Mostrar un gráfico rápido de prueba
st.subheader("Monitoreo de Sensores")
import pandas as pd
import numpy as np

chart_data = pd.DataFrame(
    np.random.randn(20, 2),
    columns=['Sensor Izquierdo', 'Sensor Derecho']
)
st.line_chart(chart_data)
