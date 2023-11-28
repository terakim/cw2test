<?php
//wp_enqueue_script( 'post' );
//wp_enqueue_script( 'postbox' );
?>

<div class="wrap">
	<h1>엠샵 프리미엄 멤버스 - 폼 디자이너</h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-2" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div id="mshop-members-form-designer-edit" class="postbox ">
						<button type="button" class="handlediv button-link" aria-expanded="true">
							<span class="screen-reader-text">패널 토글: 엠샵 폼 디자이너</span>
							<span class="toggle-indicator" aria-hidden="true"></span>
						</button>
						<h2 class="hndle ui-sortable-handle">
							<span>폼 디자이너</span>
						</h2>
						<div class="inside">
							<?php MShop_Members_Meta_Box_Members_Form::output_test( get_post( $_REQUEST['post'] ) ); ?>
						</div>
					</div>
				</div>
				<div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div id="submitdiv" class="postbox ">
						<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">패널 토글: 공개하기</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>공개하기</span></h2>
						<div class="inside">
							<div class="submitbox" id="submitpost">

								<div id="minor-publishing">

									<div style="display:none;">
										<p class="submit"><input type="submit" name="save" id="save" class="button" value="저장하기"></p></div>

									<div id="minor-publishing-actions">
										<div id="save-action">
										</div>
										<div class="clear"></div>
									</div><!-- #minor-publishing-actions -->

									<div id="misc-publishing-actions">

										<div class="misc-pub-section misc-pub-post-status"><label for="post_status">상태:</label>
											<span id="post-status-display">발행됨</span>
											<a href="#post_status" class="edit-post-status hide-if-no-js"><span aria-hidden="true">편집</span> <span class="screen-reader-text">상태 편집</span></a>

											<div id="post-status-select" class="hide-if-js">
												<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="publish">
												<select name="post_status" id="post_status">
													<option selected="selected" value="publish">발행됨</option>
													<option value="pending">검토중</option>
													<option value="draft">임시 글</option>
												</select>
												<a href="#post_status" class="save-post-status hide-if-no-js button">OK</a>
												<a href="#post_status" class="cancel-post-status hide-if-no-js button-cancel">취소</a>
											</div>

										</div><!-- .misc-pub-section -->

										<div class="misc-pub-section misc-pub-visibility" id="visibility">
											가시성: <span id="post-visibility-display">공개</span>
											<a href="#visibility" class="edit-visibility hide-if-no-js"><span aria-hidden="true">편집</span> <span class="screen-reader-text">가시성 편집</span></a>

											<div id="post-visibility-select" class="hide-if-js">
												<input type="hidden" name="hidden_post_password" id="hidden-post-password" value="">
												<input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility" value="public">
												<input type="radio" name="visibility" id="visibility-radio-public" value="public" checked="checked"> <label for="visibility-radio-public" class="selectit">공개</label><br>
												<input type="radio" name="visibility" id="visibility-radio-password" value="password"> <label for="visibility-radio-password" class="selectit">비밀번호로 보호</label><br>
												<span id="password-span"><label for="post_password">비밀번호:</label> <input type="text" name="post_password" id="post_password" value="" maxlength="20"><br></span>
												<input type="radio" name="visibility" id="visibility-radio-private" value="private"> <label for="visibility-radio-private" class="selectit">비공개</label><br>

												<p>
													<a href="#visibility" class="save-post-visibility hide-if-no-js button">OK</a>
													<a href="#visibility" class="cancel-post-visibility hide-if-no-js button-cancel">취소</a>
												</p>
											</div>

										</div><!-- .misc-pub-section -->

										<div class="misc-pub-section curtime misc-pub-curtime">
											<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js"><span aria-hidden="true">편집</span> <span class="screen-reader-text">날짜와 시간 편집</span></a>
											<fieldset id="timestampdiv" class="hide-if-js">
												<legend class="screen-reader-text">날짜와 시간</legend>
												<div class="timestamp-wrap"><label><span class="screen-reader-text">월</span><select id="mm" name="mm">
															<option value="01" data-text="1월">01-1월</option>
															<option value="02" data-text="2월">02-2월</option>
															<option value="03" data-text="3월">03-3월</option>
															<option value="04" data-text="4월">04-4월</option>
															<option value="05" data-text="5월">05-5월</option>
															<option value="06" data-text="6월" selected="selected">06-6월</option>
															<option value="07" data-text="7월">07-7월</option>
															<option value="08" data-text="8월">08-8월</option>
															<option value="09" data-text="9월">09-9월</option>
															<option value="10" data-text="10월">10-10월</option>
															<option value="11" data-text="11월">11-11월</option>
															<option value="12" data-text="12월">12-12월</option>
														</select></label> <label><span class="screen-reader-text">일</span><input type="text" id="jj" name="jj" value="30" size="2" maxlength="2" autocomplete="off"></label>, <label><span class="screen-reader-text">년도</span><input type="text" id="aa" name="aa" value="2016" size="4" maxlength="4" autocomplete="off"></label> @ <label><span class="screen-reader-text">시간</span><input type="text" id="hh" name="hh" value="10" size="2" maxlength="2" autocomplete="off"></label> : <label><span class="screen-reader-text">분</span><input type="text" id="mn" name="mn" value="53" size="2" maxlength="2" autocomplete="off"></label></div><input type="hidden" id="ss" name="ss" value="48">

												<input type="hidden" id="hidden_mm" name="hidden_mm" value="06">
												<input type="hidden" id="cur_mm" name="cur_mm" value="07">
												<input type="hidden" id="hidden_jj" name="hidden_jj" value="30">
												<input type="hidden" id="cur_jj" name="cur_jj" value="01">
												<input type="hidden" id="hidden_aa" name="hidden_aa" value="2016">
												<input type="hidden" id="cur_aa" name="cur_aa" value="2016">
												<input type="hidden" id="hidden_hh" name="hidden_hh" value="10">
												<input type="hidden" id="cur_hh" name="cur_hh" value="12">
												<input type="hidden" id="hidden_mn" name="hidden_mn" value="53">
												<input type="hidden" id="cur_mn" name="cur_mn" value="59">

												<p>
													<a href="#edit_timestamp" class="save-timestamp hide-if-no-js button">OK</a>
													<a href="#edit_timestamp" class="cancel-timestamp hide-if-no-js button-cancel">취소</a>
												</p>
											</fieldset>
										</div>
									</div>
									<div class="clear"></div>
								</div>

								<div id="major-publishing-actions">
									<div id="delete-action">
										<a class="submitdelete deletion" href="http://192.168.10.165/wp-admin/post.php?post=4573&amp;action=trash&amp;_wpnonce=606cd09122">휴지통으로 이동</a></div>

									<div id="publishing-action">
										<span class="spinner"></span>
										<input name="original_publish" type="hidden" id="original_publish" value="업데이트">
										<input name="save" type="submit" class="button button-primary button-large" id="publish" value="업데이트">
									</div>
									<div class="clear"></div>
								</div>
							</div>

						</div>
					</div>
					<div id="mshop-members-form-designer-widget" class="postbox ">
						<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">패널 토글: 컴포넌트</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>컴포넌트</span></h2>
						<div class="inside">
							<?php MShop_Members_Meta_Box_Members_Form::output_widget( get_post( $_REQUEST['post'] ) ); ?>
						</div>
					</div>
					<div id="mshop_members_form_catdiv" class="postbox ">
						<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">패널 토글: 템플릿 카테고리</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>템플릿 카테고리</span></h2>
						<div class="inside">
							<div id="taxonomy-mshop_members_form_cat" class="categorydiv">
								<ul id="mshop_members_form_cat-tabs" class="category-tabs">
									<li class="tabs"><a href="#mshop_members_form_cat-all">모든 템플릿 카테고리</a></li>
									<li class="hide-if-no-js"><a href="#mshop_members_form_cat-pop">가장 많이 사용한 것</a></li>
								</ul>

								<div id="mshop_members_form_cat-pop" class="tabs-panel" style="display: none;">
									<ul id="mshop_members_form_catchecklist-pop" class="categorychecklist form-no-clear">

										<li id="popular-mshop_members_form_cat-138" class="popular-category">
											<label class="selectit">
												<input id="in-popular-mshop_members_form_cat-138" type="checkbox" value="138">
												회원가입			</label>
										</li>


										<li id="popular-mshop_members_form_cat-139" class="popular-category">
											<label class="selectit">
												<input id="in-popular-mshop_members_form_cat-139" type="checkbox" value="139">
												이용약관			</label>
										</li>


										<li id="popular-mshop_members_form_cat-141" class="popular-category">
											<label class="selectit">
												<input id="in-popular-mshop_members_form_cat-141" type="checkbox" value="141">
												본인인증			</label>
										</li>


										<li id="popular-mshop_members_form_cat-137" class="popular-category">
											<label class="selectit">
												<input id="in-popular-mshop_members_form_cat-137" type="checkbox" value="137">
												로그인			</label>
										</li>


										<li id="popular-mshop_members_form_cat-140" class="popular-category">
											<label class="selectit">
												<input id="in-popular-mshop_members_form_cat-140" type="checkbox" value="140">
												비밀번호찾기			</label>
										</li>


										<li id="popular-mshop_members_form_cat-142" class="popular-category">
											<label class="selectit">
												<input id="in-popular-mshop_members_form_cat-142" type="checkbox" value="142">
												권한요청			</label>
										</li>

									</ul>
								</div>

								<div id="mshop_members_form_cat-all" class="tabs-panel">
									<input type="hidden" name="tax_input[mshop_members_form_cat][]" value="0">			<ul id="mshop_members_form_catchecklist" data-wp-lists="list:mshop_members_form_cat" class="categorychecklist form-no-clear">

										<li id="mshop_members_form_cat-142" class="popular-category"><label class="selectit"><input value="142" type="checkbox" name="tax_input[mshop_members_form_cat][]" id="in-mshop_members_form_cat-142"> 권한요청</label></li>

										<li id="mshop_members_form_cat-137" class="popular-category"><label class="selectit"><input value="137" type="checkbox" name="tax_input[mshop_members_form_cat][]" id="in-mshop_members_form_cat-137"> 로그인</label></li>

										<li id="mshop_members_form_cat-141" class="popular-category"><label class="selectit"><input value="141" type="checkbox" name="tax_input[mshop_members_form_cat][]" id="in-mshop_members_form_cat-141"> 본인인증</label></li>

										<li id="mshop_members_form_cat-140" class="popular-category"><label class="selectit"><input value="140" type="checkbox" name="tax_input[mshop_members_form_cat][]" id="in-mshop_members_form_cat-140"> 비밀번호찾기</label></li>

										<li id="mshop_members_form_cat-139" class="popular-category"><label class="selectit"><input value="139" type="checkbox" name="tax_input[mshop_members_form_cat][]" id="in-mshop_members_form_cat-139"> 이용약관</label></li>

										<li id="mshop_members_form_cat-143"><label class="selectit"><input value="143" type="checkbox" name="tax_input[mshop_members_form_cat][]" id="in-mshop_members_form_cat-143"> 커스텀</label></li>

										<li id="mshop_members_form_cat-147"><label class="selectit"><input value="147" type="checkbox" name="tax_input[mshop_members_form_cat][]" id="in-mshop_members_form_cat-147"> 포스트 등록</label></li>

										<li id="mshop_members_form_cat-138" class="popular-category"><label class="selectit"><input value="138" type="checkbox" name="tax_input[mshop_members_form_cat][]" id="in-mshop_members_form_cat-138"> 회원가입</label></li>
									</ul>
								</div>
								<div id="mshop_members_form_cat-adder" class="wp-hidden-children">
									<a id="mshop_members_form_cat-add-toggle" href="#mshop_members_form_cat-add" class="hide-if-no-js taxonomy-add-new">
										+ 템플릿 카테고리 추가				</a>
									<p id="mshop_members_form_cat-add" class="category-add wp-hidden-child">
										<label class="screen-reader-text" for="newmshop_members_form_cat">템플릿 카테고리 추가</label>
										<input type="text" name="newmshop_members_form_cat" id="newmshop_members_form_cat" class="form-required form-input-tip" value="템플릿 카테고리 이름" aria-required="true">
										<label class="screen-reader-text" for="newmshop_members_form_cat_parent">
											상위 템플릿 카테고리:					</label>
										<select name="newmshop_members_form_cat_parent" id="newmshop_members_form_cat_parent" class="postform">
											<option value="-1">— 상위 템플릿 카테고리 —</option>
											<option class="level-0" value="142">권한요청</option>
											<option class="level-0" value="137">로그인</option>
											<option class="level-0" value="141">본인인증</option>
											<option class="level-0" value="140">비밀번호찾기</option>
											<option class="level-0" value="139">이용약관</option>
											<option class="level-0" value="143">커스텀</option>
											<option class="level-0" value="147">포스트 등록</option>
											<option class="level-0" value="138">회원가입</option>
										</select>
										<input type="button" id="mshop_members_form_cat-add-submit" data-wp-lists="add:mshop_members_form_catchecklist:mshop_members_form_cat-add" class="button category-add-submit" value="템플릿 카테고리 추가">
										<input type="hidden" id="_ajax_nonce-add-mshop_members_form_cat" name="_ajax_nonce-add-mshop_members_form_cat" value="9ddc9b7c98">					<span id="mshop_members_form_cat-ajax-response"></span>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
