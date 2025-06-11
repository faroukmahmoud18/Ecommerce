@extends('frontend.layouts.master')

@section('meta')
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name='copyright' content=''>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="keywords" content="online shop, purchase, cart, ecommerce site, best online shopping">
	<meta name="description" content="{{$product_detail->summary}}">
	<meta property="og:url" content="{{route('product-detail',$product_detail->slug)}}">
	<meta property="og:type" content="article">
	<meta property="og:title" content="{{$product_detail->title}}">
	<meta property="og:image" content="{{$product_detail->photo}}">
	<meta property="og:description" content="{{$product_detail->description}}">
@endsection
@section('title','E-SHOP || PRODUCT DETAIL')
@section('main-content')

		<!-- Breadcrumbs -->
		<div class="breadcrumbs">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="bread-inner">
							<ul class="bread-list">
								<li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
								<li class="active"><a href="">Shop Details</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Breadcrumbs -->
				
		<!-- Shop Single -->
		<section class="shop single section">
					<div class="container">
						<div class="row"> 
							<div class="col-12">
								<div class="row">
									<div class="col-lg-6 col-12">
										<!-- Product Slider -->
										<div class="product-gallery">
											<!-- Images slider -->
											<div class="flexslider-thumbnails">
												<ul class="slides">
													@php 
														$photo=explode(',',$product_detail->photo);
													// dd($photo);
													@endphp
													@foreach($photo as $data)
														<li data-thumb="{{$data}}" rel="adjustX:10, adjustY:">
															<img src="{{$data}}" alt="{{$data}}">
														</li>
													@endforeach
												</ul>
											</div>
											<!-- End Images slider -->
										</div>
										<!-- End Product slider -->
									</div>
									<div class="col-lg-6 col-12">
										<div class="product-des">
											<!-- Description -->
											<div class="short">
												<h4>{{$product_detail->title}}</h4>
												<div class="rating-main">
													<ul class="rating">
														@php
															$rate=ceil($product_detail->getReview->avg('rate'))
														@endphp
															@for($i=1; $i<=5; $i++)
																@if($rate>=$i)
																	<li><i class="fa fa-star"></i></li>
																@else 
																	<li><i class="fa fa-star-o"></i></li>
																@endif
															@endfor
													</ul>
													<a href="#" class="total-review">({{$product_detail['getReview']->count()}}) Review</a>
                                                </div>
                                                @php 
                                                    $after_discount=($product_detail->price-(($product_detail->price*$product_detail->discount)/100));
                                                @endphp
												<div id="product-price-section">
													<p class="price"><span class="discount" id="variant-price-display">${{number_format($after_discount,2)}}</span><s>${{number_format($product_detail->price,2)}}</s> </p>
												</div>
												<p class="description">{!!($product_detail->summary)!!}</p>
											</div>
											<!--/ End Description -->

											@if(isset($product_detail->variants) && $product_detail->variants->isNotEmpty())
											<script>
												const productVariantsData = @json($product_detail->variants->map(function($variant) {
													return [
														'id' => $variant->id,
														'color_id' => $variant->color_id,
														'size_id' => $variant->size_id,
														'specification_id' => $variant->specification_id,
														'price' => $variant->price,
														'stock' => $variant->stock,
														'sku' => $variant->sku,
														'color_name' => optional($variant->color)->name,
														'size_name' => optional($variant->size)->name,
														'specification_name' => optional($variant->specification)->name,
														'specification_value' => optional($variant->specification)->value,
													];
												}));

												// Prepare unique attributes for dropdowns
												const uniqueColors = [...new Map(productVariantsData.filter(v => v.color_id).map(item => [item.color_id, {id: item.color_id, name: item.color_name}])).values()];
												const uniqueSizes = [...new Map(productVariantsData.filter(v => v.size_id).map(item => [item.size_id, {id: item.size_id, name: item.size_name}])).values()];
												const uniqueSpecifications = [...new Map(productVariantsData.filter(v => v.specification_id).map(item => [item.specification_id, {id: item.specification_id, name: item.specification_name, value: item.specification_value}])).values()];
											</script>

											<div id="variant-selectors">
												<div class="form-group PDM">
													<label for="color_id_selector">Color:</label>
													<select id="color_id_selector" class="form-control PDS nice-select wide">
														<option value="">Select Color</option>
													</select>
												</div>
												<div class="form-group PDM">
													<label for="size_id_selector">Size:</label>
													<select id="size_id_selector" class="form-control PDS nice-select wide">
														<option value="">Select Size</option>
													</select>
												</div>
												<div class="form-group PDM">
													<label for="specification_id_selector">Specification:</label>
													<select id="specification_id_selector" class="form-control PDS nice-select wide">
														<option value="">Select Specification</option>
													</select>
												</div>
											</div>
											@endif

											<!-- Product Buy -->
											<div class="product-buy">
												<form action="{{route('single-add-to-cart')}}" method="POST" id="add-to-cart-form">
													@csrf 
													<input type="hidden" name="slug" value="{{$product_detail->slug}}"> {{-- Keep slug for now, controller might use it or variant_id --}}
													<input type="hidden" name="variant_id" id="selected_variant_id" value="">

													<div class="quantity">
														<h6>Quantity :</h6>
														<!-- Input Order -->
														<div class="input-group">
															<div class="button minus">
																<button type="button" class="btn btn-primary btn-number" disabled="disabled" data-type="minus" data-field="quant[1]">
																	<i class="ti-minus"></i>
																</button>
															</div>
															<input type="text" name="quant[1]" class="input-number"  data-min="1" data-max="1000" value="1" id="quantity">
															<div class="button plus">
																<button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[1]">
																	<i class="ti-plus"></i>
																</button>
															</div>
														</div>
													<!--/ End Input Order -->
													</div>
													<div class="add-to-cart mt-4">
														<button type="submit" class="btn" id="add-to-cart-button" disabled>Add to cart</button>
														<a href="{{route('add-to-wishlist',$product_detail->slug)}}" class="btn min"><i class="ti-heart"></i></a>
													</div>
												</form>

												<p class="cat">Category :<a href="{{route('product-cat',$product_detail->cat_info['slug'])}}">{{$product_detail->cat_info['title']}}</a></p>
												@if($product_detail->sub_cat_info)
												<p class="cat mt-1">Sub Category :<a href="{{route('product-sub-cat',[$product_detail->cat_info['slug'],$product_detail->sub_cat_info['slug']])}}">{{$product_detail->sub_cat_info['title']}}</a></p>
												@endif
												<p id="variant-availability" class="availability">Stock :
													@if(isset($product_detail->variants) && $product_detail->variants->isNotEmpty())
														<span class="badge badge-warning">Please select options</span>
													@elseif($product_detail->stock > 0)
														<span class="badge badge-success">{{$product_detail->stock}}</span>
													@else
														<span class="badge badge-danger">{{$product_detail->stock}}</span>
													@endif
												</p>
											</div>
											<!--/ End Product Buy -->
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-12">
										<div class="product-info">
											<div class="nav-main">
												<!-- Tab Nav -->
												<ul class="nav nav-tabs" id="myTab" role="tablist">
													<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#description" role="tab">Description</a></li>
													<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reviews" role="tab">Reviews</a></li>
												</ul>
												<!--/ End Tab Nav -->
											</div>
											<div class="tab-content" id="myTabContent">
												<!-- Description Tab -->
												<div class="tab-pane fade show active" id="description" role="tabpanel">
													<div class="tab-single">
														<div class="row">
															<div class="col-12">
																<div class="single-des">
																	<p>{!! ($product_detail->description) !!}</p>
																</div>
															</div>
														</div>
													</div>
												</div>
												<!--/ End Description Tab -->
												<!-- Reviews Tab -->
												<div class="tab-pane fade" id="reviews" role="tabpanel">
													<div class="tab-single review-panel">
														<div class="row">
															<div class="col-12">
																
																<!-- Review -->
																<div class="comment-review">
																	<div class="add-review">
																		<h5>Add A Review</h5>
																		<p>Your email address will not be published. Required fields are marked</p>
																	</div>
																	<h4>Your Rating <span class="text-danger">*</span></h4>
																	<div class="review-inner">
																			<!-- Form -->
																@auth
																<form class="form" method="post" action="{{route('review.store',$product_detail->slug)}}">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-lg-12 col-12">
                                                                            <div class="rating_box">
                                                                                  <div class="star-rating">
                                                                                    <div class="star-rating__wrap">
                                                                                      <input class="star-rating__input" id="star-rating-5" type="radio" name="rate" value="5">
                                                                                      <label class="star-rating__ico fa fa-star-o" for="star-rating-5" title="5 out of 5 stars"></label>
                                                                                      <input class="star-rating__input" id="star-rating-4" type="radio" name="rate" value="4">
                                                                                      <label class="star-rating__ico fa fa-star-o" for="star-rating-4" title="4 out of 5 stars"></label>
                                                                                      <input class="star-rating__input" id="star-rating-3" type="radio" name="rate" value="3">
                                                                                      <label class="star-rating__ico fa fa-star-o" for="star-rating-3" title="3 out of 5 stars"></label>
                                                                                      <input class="star-rating__input" id="star-rating-2" type="radio" name="rate" value="2">
                                                                                      <label class="star-rating__ico fa fa-star-o" for="star-rating-2" title="2 out of 5 stars"></label>
                                                                                      <input class="star-rating__input" id="star-rating-1" type="radio" name="rate" value="1">
																					  <label class="star-rating__ico fa fa-star-o" for="star-rating-1" title="1 out of 5 stars"></label>
																					  @error('rate')
																						<span class="text-danger">{{$message}}</span>
																					  @enderror
                                                                                    </div>
                                                                                  </div>
                                                                            </div>
                                                                        </div>
																		<div class="col-lg-12 col-12">
																			<div class="form-group">
																				<label>Write a review</label>
																				<textarea name="review" rows="6" placeholder="" ></textarea>
																			</div>
																		</div>
																		<div class="col-lg-12 col-12">
																			<div class="form-group button5">	
																				<button type="submit" class="btn">Submit</button>
																			</div>
																		</div>
																	</div>
																</form>
																@else 
																<p class="text-center p-5">
																	You need to <a href="{{route('login.form')}}" style="color:rgb(54, 54, 204)">Login</a> OR <a style="color:blue" href="{{route('register.form')}}">Register</a>

																</p>
																<!--/ End Form -->
																@endauth
																	</div>
																</div>
															
																<div class="ratting-main">
																	<div class="avg-ratting">
																		{{-- @php 
																			$rate=0;
																			foreach($product_detail->rate as $key=>$rate){
																				$rate +=$rate
																			}
																		@endphp --}}
																		<h4>{{ceil($product_detail->getReview->avg('rate'))}} <span>(Overall)</span></h4>
																		<span>Based on {{$product_detail->getReview->count()}} Comments</span>
																	</div>
																	@foreach($product_detail['getReview'] as $data)
																	<!-- Single Rating -->
																	<div class="single-rating">
																		<div class="rating-author">
																			@if($data->user_info['photo'])
																			<img src="{{$data->user_info['photo']}}" alt="{{$data->user_info['photo']}}">
																			@else 
																			<img src="{{asset('backend/img/avatar.png')}}" alt="Profile.jpg">
																			@endif
																		</div>
																		<div class="rating-des">
																			<h6>{{$data->user_info['name']}}</h6>
																			<div class="ratings">

																				<ul class="rating">
																					@for($i=1; $i<=5; $i++)
																						@if($data->rate>=$i)
																							<li><i class="fa fa-star"></i></li>
																						@else 
																							<li><i class="fa fa-star-o"></i></li>
																						@endif
																					@endfor
																				</ul>
																				<div class="rate-count">(<span>{{$data->rate}}</span>)</div>
																			</div>
																			<p>{{$data->review}}</p>
																		</div>
																	</div>
																	<!--/ End Single Rating -->
																	@endforeach
																</div>
																
																<!--/ End Review -->
																
															</div>
														</div>
													</div>
												</div>
												<!--/ End Reviews Tab -->
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
		</section>
		<!--/ End Shop Single -->
		
		<!-- Start Most Popular -->
	<div class="product-area most-popular related-product section">
        <div class="container">
            <div class="row">
				<div class="col-12">
					<div class="section-title">
						<h2>Related Products</h2>
					</div>
				</div>
            </div>
            <div class="row">
                {{-- {{$product_detail->rel_prods}} --}}
                <div class="col-12">
                    <div class="owl-carousel popular-slider">
                        @foreach($product_detail->rel_prods as $data)
                            @if($data->id !==$product_detail->id)
                                <!-- Start Single Product -->
                                <div class="single-product">
                                    <div class="product-img">
										<a href="{{route('product-detail',$data->slug)}}">
											@php 
												$photo=explode(',',$data->photo);
											@endphp
                                            <img class="default-img" src="{{$photo[0]}}" alt="{{$photo[0]}}">
                                            <img class="hover-img" src="{{$photo[0]}}" alt="{{$photo[0]}}">
                                            <span class="price-dec">{{$data->discount}} % Off</span>
                                                                    {{-- <span class="out-of-stock">Hot</span> --}}
                                        </a>
                                        <div class="button-head">
                                            <div class="product-action">
                                                <a data-toggle="modal" data-target="#modelExample" title="Quick View" href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
                                                <a title="Wishlist" href="#"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
                                                <a title="Compare" href="#"><i class="ti-bar-chart-alt"></i><span>Add to Compare</span></a>
                                            </div>
                                            <div class="product-action-2">
                                                <a title="Add to cart" href="#">Add to cart</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-content">
                                        <h3><a href="{{route('product-detail',$data->slug)}}">{{$data->title}}</a></h3>
                                        <div class="product-price">
                                            @php 
                                                $after_discount=($data->price-(($data->discount*$data->price)/100));
                                            @endphp
                                            <span class="old">${{number_format($data->price,2)}}</span>
                                            <span>${{number_format($after_discount,2)}}</span>
                                        </div>
                                      
                                    </div>
                                </div>
                                <!-- End Single Product -->
                                	
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
	<!-- End Most Popular Area -->
	

  <!-- Modal -->
  <div class="modal fade" id="modelExample" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="ti-close" aria-hidden="true"></span></button>
            </div>
            <div class="modal-body">
                <div class="row no-gutters">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <!-- Product Slider -->
                            <div class="product-gallery">
                                <div class="quickview-slider-active">
                                    <div class="single-slider">
                                        <img src="images/modal1.png" alt="#">
                                    </div>
                                    <div class="single-slider">
                                        <img src="images/modal2.png" alt="#">
                                    </div>
                                    <div class="single-slider">
                                        <img src="images/modal3.png" alt="#">
                                    </div>
                                    <div class="single-slider">
                                        <img src="images/modal4.png" alt="#">
                                    </div>
                                </div>
                            </div>
                        <!-- End Product slider -->
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="quickview-content">
                            <h2>Flared Shift Dress</h2>
                            <div class="quickview-ratting-review">
                                <div class="quickview-ratting-wrap">
                                    <div class="quickview-ratting">
                                        <i class="yellow fa fa-star"></i>
                                        <i class="yellow fa fa-star"></i>
                                        <i class="yellow fa fa-star"></i>
                                        <i class="yellow fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <a href="#"> (1 customer review)</a>
                                </div>
                                <div class="quickview-stock">
                                    <span><i class="fa fa-check-circle-o"></i> in stock</span>
                                </div>
                            </div>
                            <h3>$29.00</h3>
                            <div class="quickview-peragraph">
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia iste laborum ad impedit pariatur esse optio tempora sint ullam autem deleniti nam in quos qui nemo ipsum numquam.</p>
                            </div>
                            <div class="size">
                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <h5 class="title">Size</h5>
                                        <select>
                                            <option selected="selected">s</option>
                                            <option>m</option>
                                            <option>l</option>
                                            <option>xl</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <h5 class="title">Color</h5>
                                        <select>
                                            <option selected="selected">orange</option>
                                            <option>purple</option>
                                            <option>black</option>
                                            <option>pink</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="quantity">
                                <!-- Input Order -->
                                <div class="input-group">
                                    <div class="button minus">
                                        <button type="button" class="btn btn-primary btn-number" disabled="disabled" data-type="minus" data-field="quant[1]">
                                            <i class="ti-minus"></i>
                                        </button>
									</div>
                                    <input type="text" name="qty" class="input-number"  data-min="1" data-max="1000" value="1">
                                    <div class="button plus">
                                        <button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[1]">
                                            <i class="ti-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <!--/ End Input Order -->
                            </div>
                            <div class="add-to-cart">
                                <a href="#" class="btn">Add to cart</a>
                                <a href="#" class="btn min"><i class="ti-heart"></i></a>
                                <a href="#" class="btn min"><i class="fa fa-compress"></i></a>
                            </div>
                            <div class="default-social">
                                <h4 class="share-now">Share:</h4>
                                <ul>
                                    <li><a class="facebook" href="#"><i class="fa fa-facebook"></i></a></li>
                                    <li><a class="twitter" href="#"><i class="fa fa-twitter"></i></a></li>
                                    <li><a class="youtube" href="#"><i class="fa fa-pinterest-p"></i></a></li>
                                    <li><a class="dribbble" href="#"><i class="fa fa-google-plus"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal end -->

@endsection
@push('styles')
	<style>
		/* Rating */
		.rating_box {
		display: inline-flex;
		}

		.star-rating {
		font-size: 0;
		padding-left: 10px;
		padding-right: 10px;
		}

		.star-rating__wrap {
		display: inline-block;
		font-size: 1rem;
		}

		.star-rating__wrap:after {
		content: "";
		display: table;
		clear: both;
		}

		.star-rating__ico {
		float: right;
		padding-left: 2px;
		cursor: pointer;
		color: #C70039;
		font-size: 16px;
		margin-top: 5px;
		}

		.star-rating__ico:last-child {
		padding-left: 0;
		}

		.star-rating__input {
		display: none;
		}

		.star-rating__ico:hover:before,
		.star-rating__ico:hover ~ .star-rating__ico:before,
		.star-rating__input:checked ~ .star-rating__ico:before {
		content: "\F005";
		}

	</style>
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof productVariantsData !== 'undefined') {
        const colorSelect = document.getElementById('color_id_selector');
        const sizeSelect = document.getElementById('size_id_selector');
        const specSelect = document.getElementById('specification_id_selector');
        const priceDisplay = document.getElementById('variant-price-display');
        const stockDisplay = document.getElementById('variant-availability');
        const addToCartButton = document.getElementById('add-to-cart-button');
        const selectedVariantInput = document.getElementById('selected_variant_id');
        const basePriceStrikethrough = document.querySelector('#product-price-section s'); // Assuming this is the original price to strike

        function populateDropdown(selectElement, items, valueField, nameField, nameField2 = null) {
            if (!selectElement) return;
            const currentValue = selectElement.value;
            // selectElement.innerHTML = '<option value="">Select ' + selectElement.labels[0].innerText.replace(':','') + '</option>';
            // Keep "Select X" if it's the first option
            if (selectElement.options.length <=1 || selectElement.options[0].value !== "") {
                 selectElement.innerHTML = '<option value="">Select ' + selectElement.parentElement.firstElementChild.innerText.replace(':','') + '</option>';
            }

            items.forEach(item => {
                let itemName = item[nameField];
                if (nameField2 && item[nameField2]) {
                    itemName += ` - ${item[nameField2]}`;
                }
                const option = document.createElement('option');
                option.value = item[valueField];
                option.textContent = itemName;
                selectElement.appendChild(option);
            });
            selectElement.value = currentValue;
            // Re-initialize nice-select if present
            if ($(selectElement).hasClass('nice-select')) {
                $(selectElement).niceSelect('update');
            }
        }

        function updateAvailableOptions() {
            const selectedColor = colorSelect ? colorSelect.value : null;
            const selectedSize = sizeSelect ? sizeSelect.value : null;
            const selectedSpec = specSelect ? specSelect.value : null;

            let filteredVariants = productVariantsData;

            if (colorSelect) {
                let availableColors = uniqueColors;
                if (selectedSize || selectedSpec) {
                    const tempFilteredBySizeOrSpec = productVariantsData.filter(v =>
                        (!selectedSize || v.size_id == selectedSize) &&
                        (!selectedSpec || v.specification_id == selectedSpec)
                    );
                    availableColors = [...new Map(tempFilteredBySizeOrSpec.filter(v => v.color_id).map(item => [item.color_id, {id: item.color_id, name: item.color_name}])).values()];
                }
                populateDropdown(colorSelect, availableColors, 'id', 'name');
                colorSelect.value = selectedColor; // try to keep selection
            }

            if (sizeSelect) {
                let availableSizes = uniqueSizes;
                if (selectedColor || selectedSpec) {
                     const tempFilteredByColorOrSpec = productVariantsData.filter(v =>
                        (!selectedColor || v.color_id == selectedColor) &&
                        (!selectedSpec || v.specification_id == selectedSpec)
                    );
                    availableSizes = [...new Map(tempFilteredByColorOrSpec.filter(v => v.size_id).map(item => [item.size_id, {id: item.size_id, name: item.size_name}])).values()];
                }
                populateDropdown(sizeSelect, availableSizes, 'id', 'name');
                sizeSelect.value = selectedSize;
            }

            if (specSelect) {
                let availableSpecs = uniqueSpecifications;
                 if (selectedColor || selectedSize) {
                    const tempFilteredByColorOrSize = productVariantsData.filter(v =>
                        (!selectedColor || v.color_id == selectedColor) &&
                        (!selectedSize || v.size_id == selectedSize)
                    );
                    availableSpecs = [...new Map(tempFilteredByColorOrSize.filter(v => v.specification_id).map(item => [item.specification_id, {id: item.specification_id, name: item.specification_name, value: item.specification_value}])).values()];
                }
                populateDropdown(specSelect, availableSpecs, 'id', 'name', 'value');
                specSelect.value = selectedSpec;
            }
             if ($(colorSelect).hasClass('nice-select')) $(colorSelect).niceSelect('update');
             if ($(sizeSelect).hasClass('nice-select')) $(sizeSelect).niceSelect('update');
             if ($(specSelect).hasClass('nice-select')) $(specSelect).niceSelect('update');


            const finalSelectedVariant = productVariantsData.find(variant =>
                (colorSelect ? variant.color_id == selectedColor : true) &&
                (sizeSelect ? variant.size_id == selectedSize : true) &&
                (specSelect ? variant.specification_id == selectedSpec : true) &&
                // Ensure that if a selector exists, a selection must be made for a variant to be considered fully matched
                (colorSelect && uniqueColors.length > 0 ? !!selectedColor : true) &&
                (sizeSelect && uniqueSizes.length > 0 ? !!selectedSize : true) &&
                (specSelect && uniqueSpecifications.length > 0 ? !!selectedSpec : true)
            );

            if (finalSelectedVariant) {
                priceDisplay.textContent = '$' + parseFloat(finalSelectedVariant.price).toFixed(2);
                if (basePriceStrikethrough) basePriceStrikethrough.style.display = 'none'; // Optionally hide base strikethrough

                if (finalSelectedVariant.stock > 0) {
                    stockDisplay.innerHTML = `Stock : <span class="badge badge-success">${finalSelectedVariant.stock}</span>`;
                    addToCartButton.disabled = false;
                } else {
                    stockDisplay.innerHTML = `Stock : <span class="badge badge-danger">Out of Stock</span>`;
                    addToCartButton.disabled = true;
                }
                selectedVariantInput.value = finalSelectedVariant.id;
            } else {
                // Reset to default or prompt to select if not all options are chosen that could define a variant
                let requiredSelectionsMade = true;
                if (uniqueColors.length > 0 && !selectedColor) requiredSelectionsMade = false;
                if (uniqueSizes.length > 0 && !selectedSize) requiredSelectionsMade = false;
                if (uniqueSpecifications.length > 0 && !selectedSpec) requiredSelectionsMade = false;

                if (requiredSelectionsMade) { // all dropdowns that have options have a selection, but no variant matches
                     priceDisplay.textContent = 'N/A';
                     if (basePriceStrikethrough) basePriceStrikethrough.style.display = 'inline';
                     stockDisplay.innerHTML = `Stock : <span class="badge badge-danger">Unavailable</span>`;
                } else { // still needs more selections
                     priceDisplay.textContent = '$' + parseFloat(@json($after_discount)).toFixed(2); // Show base price
                     if (basePriceStrikethrough) basePriceStrikethrough.style.display = 'inline';
                     stockDisplay.innerHTML = `Stock : <span class="badge badge-warning">Please select options</span>`;
                }
                addToCartButton.disabled = true;
                selectedVariantInput.value = '';
            }
        }

        if(colorSelect) colorSelect.addEventListener('change', updateAvailableOptions);
        if(sizeSelect) sizeSelect.addEventListener('change', updateAvailableOptions);
        if(specSelect) specSelect.addEventListener('change', updateAvailableOptions);

        // Initial population
        populateDropdown(colorSelect, uniqueColors, 'id', 'name');
        populateDropdown(sizeSelect, uniqueSizes, 'id', 'name');
        populateDropdown(specSelect, uniqueSpecifications, 'id', 'name', 'value');
        updateAvailableOptions(); // Call to set initial state and dependent options
    }
});
</script>

    {{-- <script>
        $('.cart').click(function(){
            var quantity=$('#quantity').val();
            var pro_id=$(this).data('id');
            // alert(quantity);
            $.ajax({
                url:"{{route('add-to-cart')}}",
                type:"POST",
                data:{
                    _token:"{{csrf_token()}}",
                    quantity:quantity,
                    pro_id:pro_id
                },
                success:function(response){
                    console.log(response);
					if(typeof(response)!='object'){
						response=$.parseJSON(response);
					}
					if(response.status){
						swal('success',response.msg,'success').then(function(){
							document.location.href=document.location.href;
						});
					}
					else{
                        swal('error',response.msg,'error').then(function(){
							document.location.href=document.location.href;
						});
                    }
                }
            })
        });
    </script> --}}

@endpush