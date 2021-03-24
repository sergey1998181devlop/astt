import {Type} from 'main.core';
import {ImageCompressor} from 'landing.imagecompressor';
import {Backend} from 'landing.backend';
import type ImageUploaderOptions from './types/imageuploader.options';
import renameX from './internal/renamex';

/**
 * @memberOf BX.Landing
 */
export class ImageUploader
{
	constructor(options: ImageUploaderOptions)
	{
		this.options = {
			uploadParams: {},
			additionalParams: {},
			dimensions: {},
			sizes: ['1x'],
			...options,
		};
	}

	getDimensions(): Array<number>
	{
		const dimensions = Object.entries(this.options.dimensions);

		return (
			this.options.sizes
				.map(size => Number.parseInt(size))
				.filter(size => Type.isNumber(size))
				.map((size) => {
					return dimensions.reduce((acc, [key, value]) => {
						acc[key] = value * size;
						return acc;
					}, {});
				})
		);
	}

	upload(file, additionalParams = {})
	{
		return Promise
			.all(
				this.getDimensions()
					.map((dimensions) => {
						return ImageCompressor.compress(file, dimensions);
					}),
			)
			.then((files) => {
				const uploadParams = {
					...this.options.uploadParams,
					...this.options.additionalParams,
					...additionalParams,
				};
				const uploads = files.map((currentFile, index) => {
					const {name} = currentFile;
					Object.defineProperty(currentFile, 'name', {
						get: () => renameX(name, index + 1),
						configurable: true,
					});

					return Backend.getInstance()
						.upload(currentFile, uploadParams);
				});

				return Promise.all(uploads);
			});
	}
}