#!/usr/bin/env bash

# Name of Session
SESSION_NAME="TC"

# Editor
EDITOR="nvim"

# Requirement
PHP="php8.4"
PHP_VERSION="8.4"
NODE=18

# Tmux window name
SERVER_WINDOW_NAME="S"
EDITOR_WINDOW_NAME="E"
CLIENT_WINDOW_NAME="C"
EXTRA_WINDOW_NAME="Ex"
DATABASE_WINDOW_NAME="D"

log() {
    printf "\033[1;${1}m${2}\033[0m: ${3}\n"
}

log_error() {
    log "31" "Error" "${1}"
}

log_warning() {
    log "33" "Warning" "${1}"
}

log_info() {
    log "34" "Info" "${1}"
}

if command -v tmux >/dev/null 2>&1; then
    log_info "Tmux is installed"
    if type -P tmux >/dev/null 2>&1; then

        log_info "Tmux is executable"
        tmux new-session -d -s "${SESSION_NAME}" -n "${SERVER_WINDOW_NAME}"

        log_info "Tmux session is created with ${SESSION_NAME}"
        if command -v ${PHP} >/dev/null 2>&1; then
            tmux send-keys -t "${SESSION_NAME}" "php8.4 artisan serve" Enter

        elif  command -v "php" >/dev/null 2>&1; then

            CURRENT_PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2)
            # This is  from stack overflow
            version_gt() {
                test "$(printf '%s\n' "$@" | sort -V | head -n 1)" != "$1";
            }

            if version_gt ${CURRENT_PHP_VERSION} ${PHP_VERSION} ; then
                tmux send-keys -t "${SESSION_NAME}" "php artisan serve" Enter
            else
                log_error "php version is not greater then or equal to ${PHP_VERSION}"
            fi
        else
            log_error "php is not installed"
        fi

        tmux new-window -n "${EDITOR_WINDOW_NAME}" -t "${SESSION_NAME}"

        if command -v ${EDITOR} >/dev/null 2>&1; then

            tmux send-keys -t "${SESSION_NAME}" "${EDITOR} ." Enter
        else
            log_error "neovim is not installed"
        fi

        if command -v "node" >/dev/null 2>&1;  then

            CURRENT_NODE_VERSION=$(node -v | sed 's/^v//' | cut -d. -f1)

            if [ ${NODE} -lt ${CURRENT_NODE_VERSION} ]; then

                tmux new-window -n "${CLIENT_WINDOW_NAME}" -t "${SESSION_NAME}"
                tmux send-keys -t "${SESSION_NAME}" "npm run dev" Enter
            else
                log_error "node version is les then required node version"
            fi
        else
            log_error "node is not installed"
        fi

        tmux new-window -n "${DATABASE_WINDOW_NAME}" -t "${SESSION_NAME}"
        tmux send-keys -t "${SESSION_NAME}" "cd database" Enter

        tmux new-window -n "${EXTRA_WINDOW_NAME}" -t "${SESSION_NAME}"
        tmux send-keys -t "${SESSION_NAME}" "echo 'Hello from tmux'" Enter

        if command -v ${EDITOR} >/dev/null 2>&1; then
            tmux select-window -t "${SESSION_NAME}:E"
        else
            tmux choose-window
        fi

        tmux attach -t "${SESSION_NAME}"
    else
        log_error "Tmux is not executable"
    fi
else
    log_error "Tmux is not installed.
    you must install tmux to continue"
fi

