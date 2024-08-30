#!/bin/bash

# Variables
DB_NAME="BananaPlex"
DB_USER="Grupo3"
DB_PASSWORD="gestiongrupo3"
BACKUP_PATH="/backups"
REMOTE_USER="fabian"
REMOTE_HOST="172.30.102.119"
REMOTE_PATH="/home/fabian/backups"

# Fecha actual
DATE=$(date +"%Y-%m-%d_%H-%M-%S")

# Archivo de backup
BACKUP_FILE="$BACKUP_PATH/$DB_NAME-$DATE.sql"

# Log file
LOG_FILE="/var/log/backup_debug.log"

# Configurar el entorno de PostgreSQL
{
    echo "[$(date)] Iniciando el script de backup"
    export PGPASSWORD=$DB_PASSWORD
    export PGUSER=$DB_USER
    export PGDATABASE=$DB_NAME
    export PGHOST="db"

    echo "[$(date)] Variables de entorno configuradas"
    echo "[$(date)] Creando backup de PostgreSQL"

    # Crear el backup de PostgreSQL
    if pg_dump -h db -U $DB_USER -d $DB_NAME -f $BACKUP_FILE; then
        echo "[$(date)] Backup creado exitosamente en $BACKUP_FILE"
    else
        echo "[$(date)] Error al crear el backup"
        exit 1
    fi

    # Transferir el backup al servidor remoto
    echo "[$(date)] Transfiriendo backup al servidor remoto"
    if scp $BACKUP_FILE $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH; then
        echo "[$(date)] Backup transferido exitosamente al servidor remoto"
    else
        echo "[$(date)] Error al transferir el backup"
        exit 1
    fi

    # Eliminar el archivo de backup local después de transferirlo
    rm $BACKUP_FILE
    echo "[$(date)] Archivo de backup local eliminado"
} >> $LOG_FILE 2>&1
