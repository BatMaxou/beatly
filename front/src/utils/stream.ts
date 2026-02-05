export const streamToUrl = async (stream: ReadableStream<Uint8Array>, mimeType: string): Promise<string> => {
  const response = new Response(stream);
  const arrayBuffer = await response.arrayBuffer();
  const blob = new Blob([arrayBuffer], { type: mimeType });

  return URL.createObjectURL(blob);
}

export const streamToAudioUrl = async (stream: ReadableStream): Promise<string> => {
  return streamToUrl(stream, 'audio/mpeg');
};
