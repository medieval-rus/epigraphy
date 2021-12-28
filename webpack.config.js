/*
 * This file is part of «Epigraphy of Medieval Rus» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus» database is distributed
 * in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus» database,
 * see <http://www.gnu.org/licenses/>.
 */

const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
    .addStyleEntry('css/site/index/index', './assets/scss/pages/site/index/index.scss')
    .addStyleEntry('css/site/content/post', './assets/scss/pages/site/content/post.scss')
    .addStyleEntry('css/site/security/login', './assets/scss/pages/site/security/login.scss')
    .addStyleEntry('css/site/inscription/list', './assets/scss/pages/site/inscription/list.scss')
    .addStyleEntry('css/site/inscription/show', './assets/scss/pages/site/inscription/show.scss')
    .addEntry('js/site/inscription/show', './assets/js/pages/site/inscription/show.js')
    .addStyleEntry('css/site/bibliographic-record/list', './assets/scss/pages/site/bibliographic-record/list.scss')
    .addStyleEntry('css/admin/inscription/edit', './assets/scss/pages/admin/inscription/edit.scss')
    .addEntry('js/admin/inscription/edit', './assets/js/pages/admin/inscription/edit.js')
    .copyFiles({
        from: './assets/fonts',
        to: 'fonts/[path][name].[ext]',
    })
;

module.exports = Encore.getWebpackConfig();
