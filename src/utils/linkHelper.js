import React from 'react';
import api from '@app/api';
import {ROUTES} from '@app/constants';
import {navigateRoute, replaceRoute} from '@app/route';
import _ from 'lodash';
import {toastAlert} from './toastAlert';

async function onPressLink(link) {
  console.log('link', link);
  if (!_.isEmpty(link)) {
    const res = await api.generateLink(link);
    if (res) {
      if (res.type) {
        switch (res.type) {
          case 'category':
          case 'brand':
            navigateRoute(ROUTES.CATEGORY_DETAIL, {
              id_category: res.id_category || 0,
              category: res.category || null,
              brand: res.brand || null,
              id_brand: res.id_brand || 0,
              defaultFilter: res.param || null,
            });
            break;
          case 'product_detail':
            navigateRoute(
              ROUTES.DETAIL_PRODUCT,
              {id: res.id},
              `product_detail_${res.id}`,
            );
            break;
          case 'content_static':
            navigateRoute(ROUTES.STATIC_BLOG, {id: res.id});
            break;
          default:
            break;
        }
      } else if (_.isArray(res)) {
        navigateRoute(ROUTES.BLOG_PAGE, {blocks: res}, `block_page_${link}`);
      } else {
        // toastAlert('Hiện chưa có đường dẫn !');
      }
    }
  } else {
    // toastAlert('Hiện chưa có đường dẫn !');
  }
}

export {onPressLink};
