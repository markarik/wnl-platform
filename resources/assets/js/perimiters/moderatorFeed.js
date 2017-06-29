import { createPerimeter } from 'vue-kindergarten';

createPerimeter({
	purpose: 'article',

	can: {
		read: () => true,
},

secretNotes(article) {
	this.guard('update', article);

	return article.secretNotes;
},

isAdmin() {
	return this.child.role === 'admin';
},

isModerator() {
	return this.child.role === 'moderator';
},

isCreator(article) {
	return this.child.id === article.author.id;
},

expose: [
'secretNotes'
]
});