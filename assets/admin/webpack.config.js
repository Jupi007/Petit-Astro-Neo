/* eslint-disable flowtype/require-valid-file-annotation */
/* eslint-disable import/no-nodejs-modules*/
/* eslint-disable no-undef */
const path = require('path');
const webpackConfig = require('../../vendor/sulu/sulu/webpack.config.js');

module.exports = (env, argv) => {
    env = env ? env : {};
    argv = argv ? argv : {};

    env.project_root_path = path.resolve(__dirname, '..', '..');
    env.node_modules_path = path.resolve(__dirname, 'node_modules');

    const config = webpackConfig(env, argv);
    config.entry = path.resolve(__dirname, 'index.js');

    /* extends security action icons with custom ones of project */
    const actionIconJsPath = path.resolve(env.node_modules_path, 'sulu-security-bundle/utils/Permission/getActionIcon.js');
    config.resolve.alias[actionIconJsPath] = path.resolve(__dirname, 'overwrites/getActionIcon.js');

    /* extends security action icons with custom ones of project */
    const ckeditor5Style = path.resolve(env.node_modules_path, 'sulu-admin-bundle/containers/CKEditor5/ckeditor5.scss');
    config.resolve.alias[ckeditor5Style] = path.resolve(__dirname, 'overwrites/ckeditor5.scss');

    return config;
};
