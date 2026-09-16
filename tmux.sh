#!/bin/env sh

SESSION_NAME="tiny-commerce"
EDITOR="nvim"
PHP="php8.4"
PHP_VERSION="8.4"
NODE=18

if command -v tmux >/dev/null 2>&1; then
    if type -P tmux >/dev/null 2>&1; then

        tmux new-session -d -s "${SESSION_NAME}" -n "S"

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
                echo "Error: php version is not greater then or equal to ${PHP_VERSION}"
            fi
        else
            echo "Error: php is not installed"
        fi

        if command -v ${EDITOR} >/dev/null 2>&1; then
            tmux new-window -n "E" -t "${SESSION_NAME}"
            tmux send-keys -t "${SESSION_NAME}" "${EDITOR} ." Enter
        else
            echo "ERROR: nvim is not installed";
        fi

        if command -v "node" >/dev/null 2>&1;  then
            CURRENT_NODE_VERSION=$(node -v | sed 's/^v//' | cut -d. -f1)
            if [ ${NODE} -lt ${CURRENT_NODE_VERSION} ]; then
                tmux new-window -n "C" -t "${SESSION_NAME}"
                tmux send-keys -t "${SESSION_NAME}" "npm run dev" Enter
            else
                echo "ERROR: node version is less then required node version"
            fi
        else
            echo "ERROR: node is not installed"
        fi

        tmux new-window -n "Ex" -t "${SESSION_NAME}"
        tmux send-keys -t "${SESSION_NAME}" "echo 'Hello from tmux'" Enter

        tmux new-window -n "D" -t "${SESSION_NAME}"
        tmux send-keys -t "${SESSION_NAME}" "cd database" Enter

        if command -v ${EDITOR} >/dev/null 2>&1; then
            tmux select-window -t "${SESSION_NAME}:E"
        else
            tmux choose-window
        fi

        tmux attach -t "${SESSION_NAME}"
    else
        echo "Error: Tmux is not executable"
    fi
else
    echo "Error: Tmux is not installed. Install tmux to continue"
fi

