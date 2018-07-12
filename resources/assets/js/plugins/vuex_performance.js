export default (store) => {
	let mutationLabel = 'mutation';
	let actionLabel = 'action'
	console.time(mutationLabel);
	console.time(actionLabel);

	store.subscribe((mutation, state) => {
		console.info('MUTATION', mutation.type, `time since previous mutation`)
		console.timeEnd(mutationLabel)
		console.time(mutationLabel)
	});

	store.subscribeAction((action, state) => {
		console.info('ACTION', action, `time since previous action`)
		console.timeEnd(actionLabel);
		console.time(actionLabel)
	})
}
