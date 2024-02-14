<?php declare(strict_types = 1);

namespace Drupal\wishlist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\NodeType;
use Goutte\Client;
use Drupal\wishlist\ProductScrapper;

/**
 * Provides a Wishlist form.
 */
final class PopinScrapForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'wishlist_popin_scrap';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['entete'] = [
      '#markup' => '<p>Voulez-vous ajouter un produit depuis une page web externe ?</p>',
    ];

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => 'URL',
      '#description' => 'Merci de renseigner une url au format https://www.example.com/produit',
    ];

    $form['actions']['yes'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => 'Oui',
        '#submit' => ['::yesScrap'],
      ],
    ];

    $form['actions']['no'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => 'Non',
        '#submit' => ['::noScrap'],
      ],
    ];

    

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
     // Utilisez la classe ProductScrapper pour extraire les informations de la page
     $url = $form_state->getValue('url');
     $scrapper = new ProductScrapper();
     $scrapedData = $scrapper->findProductData($url);
     
 
     // Redirigez vers le formulaire de création de nœud en incluant les données extraites
     $wid = \Drupal::request()->query->get('wid');
     $form_state->setRedirectUrl(Url::fromRoute(
       'node.add', 
       ['node_type' => 'wishlist_item'], 
       ['query' => [
         'wid' => $wid,
         'urlToScrap' => $url,
         'scrapedData' => $scrapedData,
       ]]
     ));
   }

   public function yesScrap(array &$form, FormStateInterface $form_state): void {
    $this->messenger()->addStatus($this->t('Ok'));

    $url = $form_state->getValue('url');
    $scrapper = new ProductScrapper();
    $scrapedData = $scrapper->findProductData($url);

    // Récupérez le type de contenu wishlist_item.
    $type = 'wishlist_item';

    // Utilisez l'injection de dépendance pour obtenir le gestionnaire d'entité.
    $entityFieldManager = \Drupal::service('entity_field.manager');

    // Obtenez les champs du type de contenu wishlist_item.
    $fields = $entityFieldManager->getFieldDefinitions('node', $type);

    // Parcourez les champs et affichez les noms.
    foreach ($fields as $fieldName => $field) {
      \Drupal::logger('wishlist')->notice('Champ : @fieldName', ['@fieldName' => $fieldName]);
    }

    // Redirigez vers le formulaire de création de nœud en incluant les données extraites.
    $wid = \Drupal::request()->query->get('wid');
    $form_state->setRedirect('node.add', [
      'node_type' => $type,
      'wid' => $wid,
      'urlToScrap' => $url,
      'scrapedData' => $scrapedData,
      // ... ajoutez d'autres champs selon vos besoins.
    ]);

    // Pré-remplissez les champs du formulaire.
    $form_state->setValue('title', isset($scrapedData['title']) ? $scrapedData['title'] : '');
    $form_state->setValue('field_price', isset($scrapedData['price']) ? $scrapedData['price'] : '');
    $form_state->setValue('field_description', isset($scrapedData['description']) ? $scrapedData['description'] : '');
    $form_state->setValue('field_image_product', isset($scrapedData['image']) ? $scrapedData['image'] : '');

    \Drupal::logger('wishlist')->notice('Champ title : @title', ['@title' => $scrapedData['title']]);
    \Drupal::logger('wishlist')->notice('Champ price : @price', ['@price' => $scrapedData['price']]);
    \Drupal::logger('wishlist')->notice('Champ description : @description', ['@description' => $scrapedData['description']]);
    \Drupal::logger('wishlist')->notice('Champ image : @image', ['@image' => $scrapedData['image']]);
  }







  public function noScrap(array &$form, FormStateInterface $form_state): void {
    $this->messenger()->addStatus($this->t('NOT Ok'));

    $wid = \Drupal::request()->query->get('wid');
    $form_state->setRedirectUrl(Url::fromRoute(
      'node.add', 
      ['node_type' => 'wishlist_item'], 
      ['query' => ['wid' => $wid]]
    ));
  }

}
