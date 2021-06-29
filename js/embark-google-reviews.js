window.onload = function () {

    (function Embark_GoogleReviews() {

        const getConfig = el => {
            let data = {};
            [].forEach.call(el.attributes, function (attr) {
                if (/^data-/.test(attr.name)) {
                    let camelCaseName = attr.name.substr(5).replace(/-(.)/g, function ($0, $1) {
                        return $1.toUpperCase();
                    });

                    let val = attr.value;

                    if(val && !isNaN(val)) {
                        val = parseInt(val);
                    }

                    if(val === "true") {
                        val = true;
                    }

                    if(val === "false") {
                        val = false;
                    }

                    data[camelCaseName] = val;
                }
            });
            
            return data;
        }

        const filterReviewsByMinRating = (reviews, minRating) => {
            if (reviews === void 0) {
                return [];
            } else {
                for (var i = reviews.length - 1; i >= 0; i--) {
                    var review = reviews[i];
                    if (review.rating < minRating) {
                        reviews.splice(i, 1);
                    }
                }
                return reviews;
            }
        };

        const sortReviewsByDateDesc = (reviews) => {
            if (typeof reviews != "undefined" && reviews != null && reviews.length != null && reviews.length > 0) {
                return reviews.sort(function (a, b) {
                    return (a.time > b.time) ? 1 : ((b.time > a.time) ? -1 : 0);
                }).reverse();
            } else {
                return []
            }
        }

        const sanitizedReviewText = (text) => {
            text = text.replace("<script>", "");
            text = text.replace("<iframe>", "");
            return text
        }


        const renderStars = (rating) => {
            var stars = '<div class="review-stars"><ul>';
            // fills gold stars
            for (var i = 0; i < rating; i++) {
                stars += '<li class="star"><i class="star"></i></li>';
            }
            // fills empty stars
            if (rating < 5) {
                for (var i = 0; i < (5 - rating); i++) {
                    stars += '<li class="empty-star"><i class="star inactive"></i></li>';
                }
            }
            stars += "</ul></div>";
            return stars;
        };

        const renderPicture = picture => {
            return "<img class='review-picture' src='" + picture + "'>";
        }

        const escapeRegExp = string => {
            return string.replace(/[.*+\-?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
        }

        const replaceAll = (str, find, replace) => {
            return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
        }

        const decodeEntities = encodedString => {
            var textArea = document.createElement('textarea');
            textArea.innerHTML = encodedString;
            return textArea.value;
        }

        const renderReviews = (reviews, targetDiv, config) => {

            let output = "";

            reviews.forEach((review, index) => {
                let picture = renderPicture(review.profile_photo_url);;
                let body = sanitizedReviewText(review.text);
                let stars = renderStars(review.rating);
                let name = review.author_name;
                let date = review.relative_time_description;
                let link = "";
                let google_icon = `<div class="review-google"></div>`;

                const replace_obj = {
                    index,
                    picture,
                    body,
                    stars,
                    name,
                    date,
                    link,
                    google_icon
                };

                var template_string = config.template;


                Object.keys(replace_obj).forEach(key => {
                    template_string = replaceAll(template_string, `{{${key}}}`, replace_obj[key]);
                });
                
                output += template_string;
            });

            targetDiv.innerHTML = output;
        };

        const targetContainers = document.querySelectorAll(".reviews-container");
        const template = document.querySelector("#embark-reviews-html-template");

        targetContainers.forEach(targetContainer => {
            const config = getConfig(targetContainer);
            config.template = template.innerHTML;

            const targetDiv = targetContainer.querySelector(".reviews-grid");
            const service = new google.maps.places.PlacesService(targetDiv);

            if (config.placeId === undefined || config.placeId === "") {
                console.error("No Place ID defined");
                return
            }

            service.getDetails({
                placeId: config.placeId
            }, (place, status) => {
                if (status == google.maps.places.PlacesServiceStatus.OK) {

                    const filteredReviews = filterReviewsByMinRating(place.reviews, config.minRating);
                    const sortedReviews = sortReviewsByDateDesc(filteredReviews);

                    if (sortedReviews.length > 0) {
                        renderReviews(sortedReviews, targetDiv, config);
                        if(config.isSlider) {
                            jQuery(targetDiv).slick({
                                'slidesToShow': 3,
                                'autoplay': true,
                                'infinite': true,
                                'arrows': true,
                                'prevArrow': jQuery(targetContainer).find('.reviews-arrow.prev'),
                                'nextArrow': jQuery(targetContainer).find('.reviews-arrow.next'),
                                'adaptiveHeight': false,
            
                                responsive: [{
                                        breakpoint: 1351,
                                        settings: {
                                            slidesToShow: 2,
                                            adaptiveHeight: false,
                                        }
                                    },
                                    {
                                        breakpoint: 1024,
                                        settings: {
                                            slidesToShow: 1,
                                            adaptiveHeight: true,
                                            arrows: true
                                        }
                                    },
                                    {
                                        breakpoint: 768,
                                        settings: {
                                            slidesToShow: 1,
                                            adaptiveHeight: true,
                                            arrows: false
                                        }
                                    }
                                ]
                            });
                        }

                        if(config.readMore) {
                            window.shown = [];
				
                            jQuery(targetContainer).find(".review-body a").click(function() {
                                const para = jQuery(this).parent().parent();
                                const id = para.data("review-index");
                                const origHeight = para.height();
                                if(!para.hasClass("open")) {
                                    para.addClass("open");
                                    const realHeight = para.height();
                                    if(realHeight < origHeight) {
                                        para.removeClass("open");
                                    }
                                    jQuery(this).hide();
                                }
                                
                                if(shown.indexOf(id) < 0) {
                                    shown.push(id);
                                    const doms =jQuery(targetContainer).find(`[data-review-index="${id}"]`);
                                    doms.find(".review-read-more a").click();
                                }
                            });
                        }
                    }

                }
            });
        });

    })();

}