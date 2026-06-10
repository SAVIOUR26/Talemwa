class Prayer {
  final String  message;
  final String? contact;

  const Prayer({required this.message, this.contact});

  Map<String, dynamic> toJson() => {
    'message': message,
    if (contact != null) 'contact': contact,
  };
}
