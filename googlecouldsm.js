const { SecretManagerServiceClient } = require('@google-cloud/secret-manager');

const client = new SecretManagerServiceClient();
const secretName = 'projects/248317208047/secrets/fb_ipa';



async function getApiSecret() { // Change function name to reflect its purpose
  try {
    const [version] = await client.accessSecretVersion({ name: secretName });
    const apiKey = version.payload.data.toString('utf8');
    return apiKey; // Return the API key
  } catch (error) {
    console.error('Error accessing secret:', error);
    return null; // Handle errors gracefully
  }
}

getApiSecret();




"AIzaSyDjWjuCwnIyNFkH1K5Kfy1DAbn_jkyXssw"