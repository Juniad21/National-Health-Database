#!/bin/sh
git filter-branch -f --env-filter '
if echo "$GIT_AUTHOR_NAME" | grep -qi "mishkat9"; then
    export GIT_AUTHOR_NAME="mishkatuljannat9"
    export GIT_AUTHOR_EMAIL="mishkatul.jannat@g.bracu.ac.bd"
fi
if echo "$GIT_COMMITTER_NAME" | grep -qi "mishkat9"; then
    export GIT_COMMITTER_NAME="mishkatuljannat9"
    export GIT_COMMITTER_EMAIL="mishkatul.jannat@g.bracu.ac.bd"
fi
if echo "$GIT_AUTHOR_NAME" | grep -qi "Antigravity"; then
    export GIT_AUTHOR_NAME="mishkatuljannat9"
    export GIT_AUTHOR_EMAIL="mishkatul.jannat@g.bracu.ac.bd"
fi
if echo "$GIT_COMMITTER_NAME" | grep -qi "Antigravity"; then
    export GIT_COMMITTER_NAME="mishkatuljannat9"
    export GIT_COMMITTER_EMAIL="mishkatul.jannat@g.bracu.ac.bd"
fi
' -- --all
