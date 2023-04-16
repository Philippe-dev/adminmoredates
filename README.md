Dotclear 2 plugin
=================

Ce petit plugin permet d'afficher, dans les listes de billets et de pages, deux colonnes supplémentaires optionnelles :

- l'une avec la date de création du billet
- l'autre avec celle de sa dernière mise à jour

Ces options sont disponibles dans Mes Préférences > Mes options > Colonnes optionnelles affichées dans les listes

Côté public, vous pouvez utiliser les balises de template suivantes :

- Date: {{tpl:EntryDate}}
- Created:{{tpl:EntryDate creadt="1"}}
- Updated: {{tpl:EntryDate upddt="1"}}
