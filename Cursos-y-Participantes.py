import tkinter as tk
from tkinter import messagebox
import subprocess
import time
import os
import webbrowser
import traceback

class ServerApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Control de Servidores IDEB")
        self.root.geometry("350x150")
        self.root.resizable(False, False)

        self.label = tk.Label(root, text="¿Deseas iniciar la aplicación?", font=("Arial", 12))
        self.label.pack(pady=15)

        self.btn_yes = tk.Button(root, text="Sí", width=10, command=self.iniciar_servidores)
        self.btn_yes.pack(side="left", padx=40, pady=20)

        self.btn_no = tk.Button(root, text="No", width=10, command=self.detener_servidores)
        self.btn_no.pack(side="right", padx=40, pady=20)

        self.php_process = None
        self.node_process = None

    def iniciar_servidores(self):
        try:
            # Inicia XAMPP Apache y MySQL (con ventana oculta)
            xampp_path = r"C:\xampp\xampp_start.exe"
            subprocess.Popen(xampp_path, shell=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)

            time.sleep(5)  # espera que inicien

            # Directorio del proyecto Laravel (dinámico)
            cwd = os.path.dirname(os.path.abspath(__file__))

            # Ocultar ventana consola con STARTUPINFO (solo Windows)
            startupinfo = subprocess.STARTUPINFO()
            startupinfo.dwFlags |= subprocess.STARTF_USESHOWWINDOW

            # Rutas absolutas de PHP y NPM
            php_path = r"C:\xampp\php\php.exe"
            npm_path = r"C:\Program Files\nodejs\npm.cmd"  # Ajusta si tu ruta es diferente

            # Iniciar Laravel (php artisan serve)
            self.php_process = subprocess.Popen(
                [php_path, "artisan", "serve", "--host=127.0.0.3", "--port=8003"],
                cwd=cwd,
                startupinfo=startupinfo
            )

            # Iniciar Vite (npm run dev)
            self.node_process = subprocess.Popen(
                [npm_path, "run", "dev", "--", "--host", "127.0.0.3"],
                cwd=cwd,
                startupinfo=startupinfo
            )

            time.sleep(5)  # espera que se levanten

            # Ruta de Chrome (dinámica usando el perfil de usuario actual y fallbacks comunes)
            user_profile = os.environ.get("USERPROFILE", "")
            chrome_path = os.path.join(user_profile, r"AppData\Local\Google\Chrome\Application\chrome.exe")
            if not os.path.exists(chrome_path):
                chrome_path = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
            if not os.path.exists(chrome_path):
                chrome_path = r"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe"

            if os.path.exists(chrome_path):
                subprocess.Popen([chrome_path, "http://127.0.0.3:8003/login"], startupinfo=startupinfo)
            else:
                # Si no está la ruta, abre en navegador predeterminado
                webbrowser.open("http://127.0.0.3:8003/login")

            messagebox.showinfo("Éxito", "Servidores iniciados correctamente.")
        except Exception as e:
            error_text = traceback.format_exc()
            print("Error al iniciar servidores:", error_text)  # Para consola
            messagebox.showerror("Error", f"No se pudieron iniciar los servidores:\n{error_text}")

    def detener_servidores(self):
        try:
            # Mata procesos PHP y node (Laravel y Vite)
            os.system('taskkill /F /IM php.exe >nul 2>&1')
            os.system('taskkill /F /IM node.exe >nul 2>&1')

            # Mata Apache y MySQL de XAMPP
            os.system('taskkill /F /IM httpd.exe >nul 2>&1')
            os.system('taskkill /F /IM mysqld.exe >nul 2>&1')

            messagebox.showinfo("Éxito", "Todos los servidores y servicios han sido detenidos.")
            self.root.destroy()
        except Exception as e:
            messagebox.showerror("Error", f"No se pudieron detener los servidores:\n{e}")

if __name__ == "__main__":
    root = tk.Tk()
    app = ServerApp(root)
    root.mainloop()
