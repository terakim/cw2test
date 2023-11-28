<?php

/*
=====================================================================================
                ﻿엠샵 주소 체크아웃 / Copyright 2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3

   우커머스 버전 : WooCommerce 2.4


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 ﻿엠샵 주소 체크아웃 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
   2. 플러그인은 사용권을 구매하는 것이며, 프로그램 저작권에 대한 구매가 아닙니다.

   3. 플러그인을 구입하여 다수의 사이트에 복사하여 사용할 수 없으며, 1개의 라이센스는 1개의 사이트에만 사용할 수 있습니다. 
      이를 위반 시 지적 재산권에 대한 손해 배상 의무를 갖습니다.

   4. 플러그인은 구입 후 1년간 업데이트를 지원합니다.

   5. 플러그인은 워드프레스, 테마, 플러그인과의 호환성에 대한 책임이 없습니다.

   6. 플러그인 설치 후 버전에 관련한 운용 및 관리의 책임은 사이트 당사자에게 있습니다.

   7. 다운로드한 플러그인은 환불되지 않습니다.

=====================================================================================
*/

?>
<style>
    .mfp-hide {
        display: none;
    }
</style>
<div id="ms_addr_1" class="edit-account msaddr-search-popup mfp-hide">
    <!-- start -->
    <div class="msaddr-search-wrap">
        <div class="msaddr-search-header">
            <p>주소검색</p>
            <p>
                <button title="%title%" class="mfp-close"><i class="mfp-close-icn">&times;</i></button>
            </p>
        </div>
        <div class="msaddr-search-keyword ">
            <input type="text" name="msaddr-keyword" value="" placeholder= "<?php _e( '검색할 주소를 입력해주세요.', 'mshop-address-ex' ); ?>">
        </div>
        <div class="msaddr-search-result">
            <table>
                <thead>
                <tr>
                    <th class="code_line"><?php _e( '우편번호', 'mshop-address-ex' ); ?></th>
                    <th class="code_line_center"><?php _e( '주소', 'mshop-address-ex' ); ?></th>
                </tr>
                </thead>
                <tbody class="code_search_list_result">
                </tbody>
            </table>
        </div>
        <div class="msaddr-pagination">
        </div>
    </div>
    <input type="hidden" class="search_result_postnum" value="">
    <input type="hidden" class="search_result_addr" value="">
    <!-- end -->
</div>
