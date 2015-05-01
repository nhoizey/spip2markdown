# SPIP → Markdown

Plugin pour le CMS [SPIP](http://spip.net) facilitant la migration de sa syntaxe spécifique vers Markdown.

## Mode d'emploi

1. Installez et activez le plugin et ses dépendances [Saisies](http://plugins.spip.net/saisies.html) et [zippeur](http://plugins.spip.net/zippeur.html)
2. Chargez la page d'un article dans le back office
3. Voilà en colonne de gauche des liens pour voir ou télécharger le contenu de l'article en Markdown — ou plutôt [Kramdown](http://kramdown.gettalong.org/syntax.html), une variante —, avec les méta données présentées dans le [YAML Front Matter](http://jekyllrb.com/docs/frontmatter/), ainsi que les éventuelles pièces jointes (images et documents)
4. Vous pouvez aussi utiliser le filtre `|spip2markdown` dans vos propres squelettes

## Éléments de syntaxe gérés

- intertitres
- gras
- italiques
- liens
- notes de bas de page
- codes en ligne et en bloc
- images
- documents
- citations
- listes ordonnées et non ordonnées, sur plusieurs niveaux

## Licence

MIT

## To do

- compléter les éléments de syntaxe manquants (tableaux, etc.)
- faire des squelettes pour les autres types de contenus (rubriques, brèves, etc.)
- remplacer les liens ```->art…```, ```->rub…```, etc. par l'URL du contenu lié
- rendre paramétrables certaines fonctionnalités :
 - forçage du téléchargement (avec nom de fichier prédéfini) vs affichage dans le navigateur
 - présence du YAML Front Matter
 - syntaxe Kramdown ou autre
 - syntaxe très spécifique des vidéos Youtube *lazyloadées* avec [Jekyll Youtube Lazyloading](https://github.com/erossignon/jekyll-youtube-lazyloading) et des embeds de tweets
- proposer un export global du site en arborescence Jekyll
- écrire des tests unitaires
- migrer en textwheel ?

