/*
 * This file is part of «Epigraphy of Medieval Rus'» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus'» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus'» database is distributed
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus'» database,
 * see <http://www.gnu.org/licenses/>.
 */

var Encore = require('@symfony/webpack-encore');

var CopyWebpackPlugin = require('copy-webpack-plugin');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    // .autoProvidejQuery()
    .cleanupOutputBeforeBuild()
    .disableSingleRuntimeChunk()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .addStyleEntry('css/site/security/login', './assets/scss/pages/site/security/login.scss')
    .addStyleEntry('css/site/inscription/list', './assets/scss/pages/site/inscription/list.scss')
    .addStyleEntry('css/site/inscription/show', './assets/scss/pages/site/inscription/show.scss')
    .addStyleEntry('css/site/bibliographic-record/list', './assets/scss/pages/site/bibliographic-record/list.scss')
    .addEntry('js/admin/common', './assets/js/pages/admin/common.js')
    .addStyleEntry('css/admin/common', './assets/scss/pages/admin/common.scss')
    .addPlugin(new CopyWebpackPlugin([
        {
            from: './assets/fonts',
            to: 'fonts'
        }
    ]))
;

module.exports = Encore.getWebpackConfig();
