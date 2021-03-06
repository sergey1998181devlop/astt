;(function() {
	"use strict";

	BX.namespace("BX.Main");

	BX.Main.ImageEditorLocale = {
		"pesdk": {
			"editor": {
				"button": {
					"new": BX.message("IMAGE_EDITOR_NEW"),
					"export": BX.message("IMAGE_EDITOR_EXPORT"),
					"cancel": BX.message("IMAGE_EDITOR_CANCEL"),
					"close": BX.message("IMAGE_EDITOR_CLOSE"),
				},
			},
			"adjustments": {
				"text": {
					"whites" : BX.message("IMAGE_EDITOR_ADJUSTMENT_WHITES"),
					"blacks" : BX.message("IMAGE_EDITOR_ADJUSTMENT_BLACKS"),
					"temperature" : BX.message("IMAGE_EDITOR_ADJUSTMENT_TEMPERATURE"),
				}
			}
		},
		"editor": {
			"button": {
				"new": BX.message("IMAGE_EDITOR_NEW"),
				"export": BX.message("IMAGE_EDITOR_EXPORT"),
				"cancel": BX.message("IMAGE_EDITOR_CANCEL"),
				"close": BX.message("IMAGE_EDITOR_CLOSE"),
			},
			"new": BX.message("IMAGE_EDITOR_NEW"),
			"export": BX.message("IMAGE_EDITOR_EXPORT"),
			"cancel": BX.message("IMAGE_EDITOR_CANCEL"),
			"close": BX.message("IMAGE_EDITOR_CLOSE"),
			"controls": {
				"library": {
					"title": BX.message("IMAGE_EDITOR_LIBRARY_TITLE"),
					"search": BX.message("IMAGE_EDITOR_LIBRARY_SEARCH"),
					"fileDropZone": BX.message("IMAGE_EDITOR_LIBRARY_DROP_ZONE"),
					"fileDropZoneHovered": BX.message("IMAGE_EDITOR_LIBRARY_DROP_ZONE_HOVERED")
				},
				"transform": {
					"title": BX.message("IMAGE_EDITOR_TRANSFORM_TITLE"),
					"reset": BX.message("IMAGE_EDITOR_TRANSFORM_RESET_TO_DEFAULT"),
					"dimensions": {
						"width": BX.message("IMAGE_EDITOR_SIZE_WIDTH"),
						"height": BX.message("IMAGE_EDITOR_SIZE_HEIGHT")
					},
					"ratios": {
						"imgly_transforms_common": {
							"name": BX.message("IMAGE_EDITOR_COMMON_CROPS"),
							"ratios": {
								"imgly_transform_common_custom": BX.message("IMAGE_EDITOR_RATIOS_CUSTOM"),
								"imgly_transform_common_square": BX.message("IMAGE_EDITOR_RATIOS_SQUARE"),
								"imgly_transform_common_4-3": "4:3",
								"imgly_transform_common_16-9": "16:9"
							}
						},
						"imgly_transforms_facebook": {
							"name": BX.message("IMAGE_EDITOR_TRANSFORM_FACEBOOK"),
							"ratios": {
								"imgly_transform_facebook_ad": BX.message("IMAGE_EDITOR_TRANSFORM_FACEBOOK_AD"),
								"imgly_transform_facebook_post": BX.message("IMAGE_EDITOR_TRANSFORM_FACEBOOK_POST"),
								"imgly_transform_facebook_cover": BX.message("IMAGE_EDITOR_TRANSFORM_FACEBOOK_COVER"),
								"imgly_transform_facebook_profile": BX.message("IMAGE_EDITOR_TRANSFORM_FACEBOOK_PROFILE")
							}
						}
					}
				},
				"filter": {
					"title": BX.message("IMAGE_EDITOR_FILTERS_TITLE"),
					"filters": {
						"identity": BX.message("IMAGE_EDITOR_FILTERS_NONE"),
						"imgly_lut_ad1920": "1920 A.D.",
						"imgly_lut_ancient": "Ancient",
						"imgly_lut_bleached": "Bleached",
						"imgly_lut_bleachedblue": "Bleached Blue",
						"imgly_lut_blues": "Blues",
						"imgly_lut_blueshadows": "Blue Shadows",
						"imgly_lut_breeze": "Breeze",
						"imgly_lut_bw": "B & W",
						"imgly_lut_classic": "Classic",
						"imgly_lut_colorful": "Colorful",
						"imgly_lut_cool": "Cool",
						"imgly_lut_cottoncandy": "Cotton Candy",
						"imgly_lut_creamy": "Creamy",
						"imgly_lut_eighties": "Eighties",
						"imgly_lut_elder": "Elder",
						"imgly_lut_evening": "Evening",
						"imgly_lut_fall": "Fall",
						"imgly_lut_food": "Food",
						"imgly_lut_glam": "Glam",
						"imgly_lut_gobblin": "Gobblin",
						"imgly_lut_highcarb": "High Carb",
						"imgly_lut_highcontrast": "High Contrast",
						"imgly_lut_k1": "K1",
						"imgly_lut_k6": "K6",
						"imgly_lut_kdynamic": "KDynamic",
						"imgly_lut_keen": "Keen",
						"imgly_lut_lenin": "Lenin",
						"imgly_lut_litho": "Litho",
						"imgly_lut_lomo100": "Lomo 100",
						"imgly_lut_lucid": "Lucid",
						"imgly_lut_neat": "Neat",
						"imgly_lut_nogreen": "No Green",
						"imgly_lut_orchid": "Orchid",
						"imgly_lut_pale": "Pale",
						"imgly_lut_pitched": "Pitched",
						"imgly_lut_plate": "Plate",
						"imgly_lut_pola669": "Pola 669",
						"imgly_lut_polasx": "Pola SX",
						"imgly_lut_pro400": "Pro 400",
						"imgly_lut_quozi": "Quozi",
						"imgly_lut_sepiahigh": "Sepia High",
						"imgly_lut_settled": "Settled",
						"imgly_lut_seventies": "Seventies",
						"imgly_lut_soft": "Soft",
						"imgly_lut_steel": "Steel",
						"imgly_lut_summer": "Summer",
						"imgly_lut_sunset": "Sunset",
						"imgly_lut_tender": "Tender",
						"imgly_lut_twilight": "Twilight",
						"imgly_lut_winter": "Winter",
						"imgly_lut_x400": "X400"
					}
				},
				"adjustments": {
					"title": BX.message("IMAGE_EDITOR_ADJUSTMENT"),
					"reset": BX.message("IMAGE_EDITOR_ADJUSTMENT_RESET"),
					"sections": {
						"basics": BX.message("IMAGE_EDITOR_ADJUSTMENT_BASIC"),
						"refinements": BX.message("IMAGE_EDITOR_ADJUSTMENT_REFINEMENTS_1")
					},
					"items": {
						"brightness": BX.message("IMAGE_EDITOR_ADJUSTMENT_BRIGHTNESS"),
						"contrast": BX.message("IMAGE_EDITOR_ADJUSTMENT_CONTRAST"),
						"saturation": BX.message("IMAGE_EDITOR_ADJUSTMENT_SATURATION"),
						"exposure": BX.message("IMAGE_EDITOR_ADJUSTMENT_EXPOSURE"),
						"gamma": BX.message("IMAGE_EDITOR_ADJUSTMENT_GAMMA"),
						"shadows": BX.message("IMAGE_EDITOR_ADJUSTMENT_SHADOWS"),
						"highlights": BX.message("IMAGE_EDITOR_ADJUSTMENT_HIGHLIGHTS"),
						"clarity" : BX.message("IMAGE_EDITOR_ADJUSTMENT_CLARITY"),
						"whites" : BX.message("IMAGE_EDITOR_ADJUSTMENT_WHITES"),
						"blacks" : BX.message("IMAGE_EDITOR_ADJUSTMENT_BLACKS"),
						"temperature" : BX.message("IMAGE_EDITOR_ADJUSTMENT_TEMPERATURE"),
					}
				},
				"focus": {
					"title": BX.message("IMAGE_EDITOR_FOCUS_TITLE"),
					"items": {
						"none": BX.message("IMAGE_EDITOR_FOCUS_NONE"),
						"radial": BX.message("IMAGE_EDITOR_FOCUS_RADIAL"),
						"mirrored": BX.message("IMAGE_EDITOR_FOCUS_MIRRORED"),
						"linear": BX.message("IMAGE_EDITOR_FOCUS_LINEAR"),
						"gaussian": BX.message("IMAGE_EDITOR_FOCUS_GAUSSIAN")
					}
				},
				"text": {
					"title": BX.message("IMAGE_EDITOR_TEXT_TITLE"),
					"defaultText": BX.message("IMAGE_EDITOR_TEXT_DEFAULT_TEXT"),
					"new": BX.message("IMAGE_EDITOR_TEXT_NEW_TEXT"),
					"font": BX.message("IMAGE_EDITOR_TEXT_FONT"),
					"size": BX.message("IMAGE_EDITOR_TEXT_SIZE"),
					"spacing": BX.message("IMAGE_EDITOR_TEXT_PARAMS"),
					"line": BX.message("IMAGE_EDITOR_TEXT_LINE_HEIGHT")
				},
				"textdesign": {
					"title": BX.message("IMAGE_EDITOR_TEXT_DESIGN")
				},
				"sticker": {
					"title": BX.message("IMAGE_EDITOR_STICKERS_TITLE"),
					"new": BX.message("IMAGE_EDITOR_STICKERS_NEW"),
					"fill": BX.message("IMAGE_EDITOR_STICKERS_FILL"),
					"opacity": BX.message("IMAGE_EDITOR_STICKERS_OPACITY"),
					"replace": BX.message("IMAGE_EDITOR_STICKERS_REPLACE"),
					"stickerCategories": {},
					"stickers": {}
				},
				"brush": {
					"title": BX.message("IMAGE_EDITOR_BRUSH_TITLE"),
					"settings": BX.message("IMAGE_EDITOR_BRUSH_SETTINGS"),
					"width": BX.message("IMAGE_EDITOR_BRUSH_WIDTH"),
					"hardness": BX.message("IMAGE_EDITOR_BRUSH_HARDNESS")
				},
				"frame": {
					"title": BX.message("IMAGE_EDITOR_FRAME_TITLE"),
					"opacity": BX.message("IMAGE_EDITOR_FRAME_OPACITY"),
					"width": BX.message("IMAGE_EDITOR_FRAME_WIDTH"),
					"replace": BX.message("IMAGE_EDITOR_FRAME_REPLACE"),
					"fill": BX.message("IMAGE_EDITOR_FRAME_FILL"),
					"frames": {
						"none": BX.message("IMAGE_EDITOR_FRAME_NONE")
					}
				},
				"overlay": {
					"title": BX.message("IMAGE_EDITOR_OVERLAY_TITLE"),
					"blendModes": {
						"none": BX.message("IMAGE_EDITOR_OVERLAY_NONE"),
						"normal": "Normal",
						"overlay": "Overlay",
						"hardLight": "Hard Light",
						"softLight": "Soft Light",
						"multiply": "Multiply",
						"darken": "Darken",
						"lighten": "Lighten",
						"screen": "Screen",
						"colorBurn": "Color Burn"
					},
					"overlays": {
						"none": BX.message("IMAGE_EDITOR_OVERLAY_NONE"),
						"imgly_overlay_bokeh": "Bokeh",
						"imgly_overlay_chop": "Chop",
						"imgly_overlay_clouds": "Clouds",
						"imgly_overlay_golden": "Golden",
						"imgly_overlay_grain": "Grain",
						"imgly_overlay_hearts": "Hearts",
						"imgly_overlay_lightleak1": "Light Leak 1",
						"imgly_overlay_lightleak2": "Light Leak 2",
						"imgly_overlay_metal": "Metal",
						"imgly_overlay_mosaic": "Mosaic",
						"imgly_overlay_painting": "Painting",
						"imgly_overlay_paper": "Paper",
						"imgly_overlay_rain": "Rain",
						"imgly_overlay_vintage": "Vintage",
						"imgly_overlay_wall1": "Wall",
						"imgly_overlay_wall2": "Wall 2",
						"imgly_overlay_wood": "Wood"
					}
				}
			}
		},
		"loading": {
			"loading": BX.message("IMAGE_EDITOR_LOADING"),
			"exporting": BX.message("IMAGE_EDITOR_EXPORTING"),
			"resizing": BX.message("IMAGE_EDITOR_RESIZING"),
			"fonts": BX.message("IMAGE_EDITOR_LOADING_FONTS")
		},
		"warnings": {
			"imageResized_maxMegaPixels": {
				"title": BX.message("IMAGE_EDITOR_IMAGE_RESIZED_TITLE"),
				"text": BX.message("IMAGE_EDITOR_IMAGE_RESIZED_DESCRIPTION")
			},
			"imageResized_maxDimensions": {
				"title": BX.message("IMAGE_EDITOR_IMAGE_RESIZED_TITLE"),
				"text": BX.message("IMAGE_EDITOR_IMAGE_RESIZED_DESCRIPTION")
			},
			"newImage_changesLost": {
				"title": BX.message("IMAGE_EDITOR_IMAGE_NEW_IMAGE_TITLE"),
				"text": BX.message("IMAGE_EDITOR_IMAGE_NEW_IMAGE_DESCRIPTION"),
				"buttons": {
					"yes": BX.message("IMAGE_EDITOR_YES"),
					"no": BX.message("IMAGE_EDITOR_NO")
				}
			},
			"discardChanges": {
				"title": BX.message("IMAGE_EDITOR_DISCARD_CHANGES_TITLE"),
				"text": BX.message("IMAGE_EDITOR_DISCARD_CHANGES_DESCRIPTION"),
				"buttons": {
					"cancel": BX.message("IMAGE_EDITOR_DISCARD_CHANGES_CANCEL_BUTTON"),
					"keep": BX.message("IMAGE_EDITOR_DISCARD_CHANGES_KEEP_CHANGES_BUTTON"),
					"discard": BX.message("IMAGE_EDITOR_DISCARD_CHANGES_DISCARD_BUTTON")
				}
			}
		},
		"errors": {
			"title": BX.message("IMAGE_EDITOR_ERROR"),
			"renderingError": {
				"text": BX.message("IMAGE_EDITOR_ERROR_RENDERING_ERROR_DESCRIPTION")
			},
			"stickerLoadingError": {
				"text": BX.message("IMAGE_EDITOR_ERROR_LOAD_STICKER")
			},
			"imageLoadingError": {
				"text": BX.message("IMAGE_EDITOR_ERROR_IMAGE_LOADING_ERROR")
			},
			"fontLoadingError": {
				"text": BX.message("IMAGE_EDITOR_ERROR_FONT_LOADING_ERROR")
			},
			"webcamNotSupported": {
				"text": BX.message("IMAGE_EDITOR_ERROR_WEBCAM_NOT_SUPPORTED")
			},
			"webcamUnavailable": {
				"text": BX.message("IMAGE_EDITOR_ERROR_WEBCAM_UNAVAILABLE")
			},
			"invalidFileType": {
				"text": BX.message("IMAGE_EDITOR_ERROR_UNSUPPORTED_FILE_TYPE")
			}
		}
	};

})();