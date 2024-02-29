# CONTRIBUTION

Afin de contribuer au projet, veuillez suivre les étapes suivantes.

## 1. Créer une Issue<br>
   Dans un premier temps, vous devrez créer [créez une nouvelle issue](https://github.com/HyacineAlnuma/PHP-P8/issues) qui correspond à la tâche que vous voulez effectuer.

## 2. Créer une branche<br>
   Pour chaque nouvelle issue vous devrez créer une nouvelle branche correspondante afin de faciliter l'intégration de nouvelle fonctionnalités sur la branche principale.

## 3. Créer une pull request<br>
   Veuillez faire une [pull request](https://github.com/HyacineAlnuma/PHP-P8/pulls) afin de soumettre toute contribution.

## 4. Validation
   La pull request devra valider les points suivants :
   * __Tests__ : Avant de soumettre la pull request, veuillez écrire les tests correspondants au code que vous avez écrit puis à lancer tous les tests pour vérifier que ceux-ci passent, grâce à la commande suivante :
```
vendor/bin/phpunit
```
   De plus, il vous faudra générer un rapport de couverture du code grâce à la commande suivante :
```
vendor/bin/phpunit --coverage-html public/test-coverage
```
   Le taux de couverture ne doit pas descendre en dessous de 100%.
   
   * __Qualité du code__ sur [CodeClimate](https://codeclimate.com/github/HyacineAlnuma/PHP-P8) :  Veillez à ne pas faire trop baisser le taux de maintenabilité.
   
   * __Review__ : Une review globale sera effectuée afin de finaliser la validation de la demande d'ajout.

## 5. Bonnes pratiques
   * Pour garantir la qualité de votre code et le respect des strandards de programmation en PHP, vous deverez veillez à respecter les conventions [PSR-1](https://www.php-fig.org/psr/psr-1/), [PSR-4](https://www.php-fig.org/psr/psr-4/), [PSR-12](https://www.php-fig.org/psr/psr-12/).
   * Lors de l'ajout de nouvelles fonctionnalités, veuillez implementer les tests correspondants.
   * Veillez à bien documenter votre code.
   * Nous vous encourageons également à vous référer à la section [The Symfony Framework Best Practices de la documentation Symfony](https://symfony.com/doc/4.4/best_practices.html)

## Merci pour votre contribution !