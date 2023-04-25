Dotclear 2 plugin
=================

Ce petit plugin permet d'afficher, dans les listes de billets et de pages, deux colonnes supplémentaires optionnelles :

- l'une avec la date de création du billet
- l'autre avec celle de sa dernière mise à jour

et de trier les listes suivant ces critères

Il affiche aussi ces dates sous le champ *Date de publication* dans la page d'édition d'un billet.

Côté public, vous pouvez utiliser les balises de template suivantes :

- Date: {{tpl:EntryDate}}
- Created:{{tpl:EntryDate creadt="1"}}
- Updated: {{tpl:EntryDate upddt="1"}}
