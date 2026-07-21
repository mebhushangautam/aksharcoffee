# Git hooks path (recommended)

If your commits get a trailing `Made-with: Cursor` line or `Co-authored-by: Cursor <cursoragent@cursor.com>`, a **global** Git hook (often from an IDE) is appending it. This repo ships **hooks in `.githooks/`** that strip those footers and block common variants, and using this directory as `core.hooksPath` avoids running those global hooks for this clone.

Run once per clone:

```bash
git config core.hooksPath .githooks
```

Hooks must be executable. If Git skips them:

```bash
chmod +x .githooks/commit-msg .githooks/prepare-commit-msg
```

This setting is **local** to your clone (not committed).

To restore default hooks for this repo:

```bash
git config --unset core.hooksPath
```
