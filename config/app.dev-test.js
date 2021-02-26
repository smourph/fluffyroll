const appConfig = {
  offline: true,

  titlePrefix: '[DEV]',

  githubDBFluffs: {
    host: 'api.github.com', // <-- Private github api url. If not passed, defaults to 'api.github.com'
    pathPrefix: null, // <-- Private github api url prefix. If not passed, defaults to null.
    protocol: 'https', // <-- http protocol 'https' or 'http'. If not passed, defaults to 'https'
    user: 'smourph', // <-- Your Github username
    repo: 'fluffyroll-db', // <-- Your repository to be used a db
    remoteFilename: 'db-fluffs.json', // <- File with extension .json
    personalAccessToken: process.env.REACT_APP_GITHUB_PERSONNAL_ACCESS_TOKEN
  }
};

module.exports = appConfig;
