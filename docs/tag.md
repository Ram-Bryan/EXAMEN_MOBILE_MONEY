
# 2. Ajouter tout
git add .

# 3. Commit
git commit -m "Version 3 - Mobile Money (livraison)"

# 4. Tag
git tag -a v3 -m "Version 3 - Livraison du 20/07/2026"

# 5. Pousser
git push origin main --tags

# 6. Vérifier
git tag
git log --oneline --decorate -3