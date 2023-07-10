<?php
namespace Netzstrategen\WooCommerceReputations;

class GraphQL {

  public static function graphql_register_types():void {
    register_graphql_field(
    'SEOPostTypeSchema',
    'trustedShopsAggregateSchema', [
      'type' => 'String',
      'resolve' => function () {
        $schema =  TrustedShops::getTrustedShopAggregateSchema();
        if (!$schema) {
          return NULL;
        }
        return \json_encode($schema, JSON_UNESCAPED_SLASHES);
      }
    ]);
  }

}
