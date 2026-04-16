# 🛡️ Lab Provisioner — Laboratorios Vulnerables con Docker

> Entornos de práctica de ciberseguridad ofensiva, automatizados y reproducibles.  
> Diseñados para estudiantes que quieren aprender hacking ético desde cero.

---

## ⚠️ Aviso legal

> **Este proyecto es exclusivamente para fines educativos.**  
> Todos los laboratorios están diseñados para ejecutarse en entornos locales y controlados.  
> El uso de estas técnicas contra sistemas sin autorización explícita es **ilegal**.  
> El autor no se hace responsable del mal uso de este material.

---

## 📋 Índice

- [¿Qué es esto?](#-qué-es-esto)
- [Requisitos](#-requisitos)
- [Estructura del proyecto](#-estructura-del-proyecto)
- [Laboratorios disponibles](#-laboratorios-disponibles)
- [Cómo usar](#-cómo-usar)
- [Alias rápidos](#-alias-rápidos)
- [Categorías y dificultad](#-categorías-y-dificultad)
- [Roadmap](#-roadmap)

---

## 🔍 ¿Qué es esto?

**Lab Provisioner** es una colección de laboratorios vulnerables que se levantan automáticamente con Docker.  
Cada laboratorio simula un escenario de ataque real, aislado y seguro para practicar.

No necesitas configurar nada a mano. Un comando y el lab está listo.

**Tecnologías usadas:**
- 🐳 Docker — contenerización de los entornos
- 🐚 Bash — scripts de automatización
- 🐍 Ansible — configuración de servicios donde tiene sentido

---

## 🧰 Requisitos

| Herramienta | Versión mínima | Para qué se usa |
|---|---|---|
| Docker | 20.x | Levantar los laboratorios |
| Bash | 4.x | Scripts de automatización |
| nmap | cualquiera | Escaneo de puertos (alumno) |
| hydra | cualquiera | Fuerza bruta (alumno) |
| Ansible | 2.x | Configuración avanzada (opcional) |

### Instalar dependencias rápido (Debian/Ubuntu)

```bash
sudo apt update
sudo apt install -y docker.io nmap hydra
sudo systemctl enable --now docker
```

---

## 📁 Estructura del proyecto

```
lab-provisioner/
│
├── README.md                    # Este archivo
├── launch.sh                    # Script maestro para lanzar cualquier lab
│
├── labs/
│   ├── basic/                   # 🟢 Laboratorios de nivel básico
│   │   ├── lab1-ssh/            # Lab 1: Acceso inicial por SSH
│   │   │   ├── Dockerfile
│   │   │   ├── build_and_run.sh
│   │   │   ├── config/
│   │   │   │   └── sshd_config
│   │   │   └── files/
│   │   │       └── flag.txt
│   │   │
│   │   └── lab2-suid/           # Lab 2: Escalada de privilegios SUID
│   │       ├── Dockerfile
│   │       ├── build_and_run.sh
│   │       └── files/
│   │           └── flag.txt
│   │
│   └── medium/                  # 🟡 Laboratorios de nivel medio
│       └── lab3-upload/         # Lab 3: Subida de archivos y webshell
│           ├── Dockerfile
│           ├── build_and_run.sh
│           ├── ansible/
│           │   ├── playbook.yml
│           │   └── inventory
│           └── files/
│               ├── index.php
│               ├── upload.php
│               └── .flag
│
└── docs/                        # Guías de uso por laboratorio
    ├── GUIDE_lab1.md
    ├── GUIDE_lab2.md
    └── GUIDE_lab3.md
```

---

## 🧪 Laboratorios disponibles

### 🟢 BASIC

| # | Nombre | Técnica principal | Puerto | Dificultad |
|---|---|---|---|---|
| Lab 1 | Acceso inicial por SSH | Banner leak + fuerza bruta | 45768 | ⭐ Fácil |
| Lab 2 | Escalada de privilegios | SUID + GTFOBins | 2222 | ⭐⭐ Fácil-Media |

### 🟡 MEDIUM

| # | Nombre | Técnica principal | Puerto | Dificultad |
|---|---|---|---|---|
| Lab 3 | Subida de archivos | File upload + Webshell (RCE) | 8888 | ⭐⭐⭐ Media |

---

## 🚀 Cómo usar

### Opción 1 — Script maestro (recomendado)

```bash
# Lanzar un lab específico
./launch.sh lab1
./launch.sh lab2
./launch.sh lab3

# Ver todos los labs disponibles
./launch.sh --list

# Parar un lab
./launch.sh --stop lab1
```

### Opción 2 — Script individual de cada lab

```bash
cd labs/basic/lab1-ssh
chmod +x build_and_run.sh
./build_and_run.sh
```

### Parar y limpiar todos los labs

```bash
docker ps                          # Ver contenedores activos
docker stop $(docker ps -q)        # Parar todos
docker rm $(docker ps -aq)         # Eliminar todos
```

---

## ⚡ Alias rápidos

Añade esto a tu `~/.bashrc` o `~/.zshrc` para lanzar labs con un solo comando:

```bash
# Lab Provisioner - alias rápidos
alias lab1='cd ~/lab-provisioner && ./launch.sh lab1'
alias lab2='cd ~/lab-provisioner && ./launch.sh lab2'
alias lab3='cd ~/lab-provisioner && ./launch.sh lab3'
alias labs='cd ~/lab-provisioner && ./launch.sh --list'
alias labs-stop='docker stop $(docker ps -q) 2>/dev/null && echo "Labs parados."'
```

Recarga la configuración:
```bash
source ~/.bashrc
```

A partir de ahí, simplemente escribe `lab1` en cualquier terminal.

---

## 🏷️ Categorías y dificultad

| Categoría | Color | Descripción |
|---|---|---|
| `BASIC` | 🟢 Verde | Técnicas fundamentales. Para quienes empiezan en pentesting. |
| `MEDIUM` | 🟡 Amarillo | Requiere entender el contexto web o del sistema. |
| `HARD` *(próximamente)* | 🔴 Rojo | Explotación encadenada, evasión, post-explotación. |

---

## 🗺️ Roadmap

- [x] Lab 1 — Acceso SSH por banner leak
- [x] Lab 2 — Escalada de privilegios SUID
- [x] Lab 3 — File upload + webshell RCE
- [ ] Lab 4 — SQL Injection (MEDIUM)
- [ ] Lab 5 — Buffer Overflow básico (HARD)
- [ ] Lab 6 — Active Directory básico (HARD)
- [ ] Script maestro con menú interactivo
- [ ] Integración con writeups automáticos

---

## 🤝 Contribuir

¿Tienes ideas para nuevos labs? Abre un issue o un pull request.  
Los labs deben seguir la estructura del proyecto y tener al menos:
- Dockerfile funcional
- Flag verificable
- Guía de uso en `docs/`

---

## 📄 Licencia

MIT — Úsalo, modifícalo, compártelo. Solo no lo uses para hacer daño.
