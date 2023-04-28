# Dotclear 2 plugin

Ce plugin permet d'afficher, dans les listes de billets ou de pages, deux colonnes supplémentaires optionnelles :

-   l'une avec la date de création
-   l'autre avec celle de la dernière mise à jour

et de trier les listes suivant ces critères

Il affiche aussi ces dates sous le champ _Date de publication_ dans la page d'édition d'un billet ou d'une page, et permet aussi de les modifier.

Côté public, vous pouvez utiliser les balises de template suivantes :

-   Date de publication : {{tpl:EntryDate}}
-   Date de création : {{tpl:EntryDate creadt="1"}}
-   Date de mise à jour : {{tpl:EntryDate upddt="1"}}
