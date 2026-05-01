#!/bin/sh
git filter-branch -f --env-filter '
if [ "$GIT_AUTHOR_NAME" = "mishkat9" ] || [ "$GIT_AUTHOR_EMAIL" = "mishkat9@users.noreply.github.com" ]; then
    export GIT_AUTHOR_NAME="mishkatuljannat9"
    export GIT_AUTHOR_EMAIL="mishkatuljannat9@users.noreply.github.com"
fi
if [ "$GIT_COMMITTER_NAME" = "mishkat9" ] || [ "$GIT_COMMITTER_EMAIL" = "mishkat9@users.noreply.github.com" ]; then
    export GIT_COMMITTER_NAME="mishkatuljannat9"
    export GIT_COMMITTER_EMAIL="mishkatuljannat9@users.noreply.github.com"
fi
' -- --all
