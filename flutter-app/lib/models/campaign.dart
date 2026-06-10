class Campaign {
  final int     id;
  final String  title;
  final String? description;
  final double  goalAmount;
  final String  currency;
  final double  raisedAmount;
  final String? deadline;
  final bool    isActive;

  const Campaign({
    required this.id,
    required this.title,
    this.description,
    required this.goalAmount,
    required this.currency,
    required this.raisedAmount,
    this.deadline,
    required this.isActive,
  });

  factory Campaign.fromJson(Map<String, dynamic> j) => Campaign(
    id:           j['id'] as int,
    title:        j['title'] as String,
    description:  j['description'] as String?,
    goalAmount:   (j['goal_amount'] as num).toDouble(),
    currency:     j['currency'] as String? ?? 'USD',
    raisedAmount: (j['raised_amount'] as num?)?.toDouble() ?? 0,
    deadline:     j['deadline'] as String?,
    isActive:     (j['is_active'] as int? ?? 1) == 1,
  );

  double get progressPercent => goalAmount > 0 ? (raisedAmount / goalAmount).clamp(0, 1) : 0;
}
