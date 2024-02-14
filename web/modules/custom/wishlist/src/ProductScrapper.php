<?php

namespace Drupal\wishlist;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class ProductScrapper.
 *
 * Extrait des informations d'une page web de produit.
 */
class ProductScrapper {

  /**
   * Extrait les données d'un produit à partir de l'URL fournie.
   *
   * @param string $url
   *   L'URL de la page du produit.
   *
   * @return array
   *   Un tableau contenant les données extraites du produit.
   */
  public function findProductData($url) {
    $httpClient = HttpClient::create(['timeout' => 10]);
    $httpBrowser = new HttpBrowser($httpClient);
    $crawler = $httpBrowser->request('GET', $url);

  //   // Affiche les données extraites pour le débogage
  //   var_dump([
  //     'title' => $this->findTitle($crawler),
  //     'price' => $this->findPrice($crawler),
  //     'description' => $this->findDescription($crawler),
  //     'image' => $this->findImage($crawler),
  // ]);

  

  

    return [
      'title' => $this->findTitle($crawler),
      'price' => $this->findPrice($crawler),
      'description' => $this->findDescription($crawler),
      'image' => $this->findImage($crawler),
    ];
  }

  /**
   * Extrait le titre de la page.
   *
   * @param \Symfony\Component\DomCrawler\Crawler $crawler
   *   L'objet Crawler représentant la page web.
   *
   * @return string
   *   Le titre extrait.
   */
  private function findTitle(Crawler $crawler) {
    return $crawler->filter('#title')->text();
  }

  /**
   * Extrait le prix de la page.
   *
   * @param \Symfony\Component\DomCrawler\Crawler $crawler
   *   L'objet Crawler représentant la page web.
   *
   * @return string
   *   Le prix extrait.
   */
  private function findPrice(Crawler $crawler) {
    // Utilisez le sélecteur CSS ou XPath approprié pour votre cas.
    $priceElement = $crawler->filter('.a-price-whole')->first();

    if ($priceElement->count() > 0) {
        // Si l'élément du prix est trouvé, récupérez le texte.
        return trim($priceElement->text());
    }

    // Si l'élément du prix n'est pas trouvé, renvoyez une valeur par défaut.
    return 'Prix non disponible';
}

  /**
   * Extrait la description de la page.
   *
   * @param \Symfony\Component\DomCrawler\Crawler $crawler
   *   L'objet Crawler représentant la page web.
   *
   * @return string
   *   La description extraite.
   */
  private function findDescription(Crawler $crawler) {
    // Mettez en œuvre la logique pour extraire la description de la page.
    // Utilisez le sélecteur CSS ou XPath approprié pour votre cas.
    $description = $crawler->filter('#feature-bullets')->text();

    // Limitez la description à un certain nombre de caractères, par exemple, 255.
    $limitedDescription = substr($description, 0, 500);

    return $limitedDescription ?: 'Description non disponible';
  }

  /**
   * Extrait l'URL de l'image de la page.
   *
   * @param \Symfony\Component\DomCrawler\Crawler $crawler
   *   L'objet Crawler représentant la page web.
   *
   * @return string
   *   L'URL de l'image extraite.
   */
  private function findImage(Crawler $crawler) {
    // Mettez en œuvre la logique pour extraire l'URL de l'image de la page.
    // Utilisez le sélecteur CSS ou XPath approprié pour votre cas.
     // Cherchez une balise img avec un attribut src.
     $imageTag = $crawler->filter('#landingImage')->first();

     if ($imageTag->count() > 0) {
         // Si une balise img est trouvée, récupérez l'URL à partir de l'attribut src.
         return $imageTag->attr('src');
     }
 
     // Si aucune balise img n'est trouvée, renvoyez une valeur par défaut.
     return 'Image non disponible';
  }

}
