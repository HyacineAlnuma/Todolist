# CONTRIBUTION

To contribute to the project, please follow the steps below.

## 1. Create an Issue<br>
   Initially, you will need to [create a new issue](https://github.com/HyacineAlnuma/PHP-P8/issues) corresponding to the task you want to implement.

## 2. Create a Branch<br>
   For each new issue, you should create a corresponding new branch to facilitate the integration of new features into the main branch.

## 3. Create a Pull Request<br>
   Please create a [pull request](https://github.com/HyacineAlnuma/PHP-P8/pulls) to submit any contributions.

## 4. Validation
   The pull request must validate the following points:
   * __Tests__ : Before submitting the pull request, please write tests corresponding to the code you have written and run all tests to verify that they pass, using the following command:
```
vendor/bin/phpunit
```
   Additionally, you will need to generate a code coverage report using the following command:
```
vendor/bin/phpunit --coverage-html public/test-coverage
```
   The coverage rate should not drop below 100%.
   
   * __Code Quality__ on [CodeClimate](https://codeclimate.com/github/HyacineAlnuma/PHP-P8) :  Please ensure not to significantly decrease the maintainability rate.
   
   * __Review__ : A comprehensive review will be conducted to finalize the validation of the addition request.

## 5. Best Practices
   * To ensure the quality of your code and adherence to PHP programming standards, please follow the conventions [PSR-1](https://www.php-fig.org/psr/psr-1/), [PSR-4](https://www.php-fig.org/psr/psr-4/), [PSR-12](https://www.php-fig.org/psr/psr-12/).
   * When adding new features, please implement corresponding tests.
   * Ensure proper documentation of your code.
   * We also encourage you to refer to the [The Symfony Framework Best Practices of the Symfony doc](https://symfony.com/doc/4.4/best_practices.html)

## Thank you for your contribution!